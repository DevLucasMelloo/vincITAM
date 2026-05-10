<?php

// Camada de acesso a dados da entidade Vinculo. Contém todo o SQL da tabela 'vinculos' e é a única camada que se comunica diretamente com o banco via PDO.

declare(strict_types=1);

namespace dao;

use models\VinculoModel;
use PDO;

class VinculoDAO
{
    // Recebe a conexão PDO via construtor (injeção de dependência) — o DAO não cria a conexão, apenas a utiliza
    public function __construct(private readonly PDO $conexao) {}

    // Insere um novo vínculo entre dois ativos e retorna true em caso de sucesso
    public function salvar(VinculoModel $vinculo): bool
    {
        $sql = "INSERT INTO vinculos (id_ativo_pai, id_ativo_filho, tipo_relacao)
                VALUES (:id_ativo_pai, :id_ativo_filho, :tipo_relacao)";

        $stmt = $this->conexao->prepare($sql); // Prepared Statement — protege contra SQL Injection (RNF-002)

        return $stmt->execute([
            'id_ativo_pai'   => $vinculo->idAtivoPai,   // camelCase no PHP → snake_case no banco (RNF-007)
            'id_ativo_filho' => $vinculo->idAtivoFilho,
            'tipo_relacao'   => $vinculo->tipoRelacao,
        ]);
    }

    // Retorna todos os vínculos do sistema com os nomes dos ativos pai e filho — usa JOIN para evitar o problema N+1 (RNF-008)
    public function buscarTodos(): array
    {
        $sql = "SELECT v.id, v.id_ativo_pai, v.id_ativo_filho, v.tipo_relacao, v.data_vinculo,
                       pai.nome AS nome_ativo_pai,
                       filho.nome AS nome_ativo_filho
                FROM vinculos v
                INNER JOIN ativos pai   ON pai.id   = v.id_ativo_pai
                INNER JOIN ativos filho ON filho.id = v.id_ativo_filho
                ORDER BY v.data_vinculo DESC";

        $stmt = $this->conexao->query($sql);

        $resultado = [];

        while ($row = $stmt->fetch()) {
            $resultado[] = $this->mapearParaModel($row);
        }

        return $resultado;
    }

    // Retorna todos os vínculos onde o ativo é o pai — lista o que está hospedado/dependente dele (RF-004)
    public function buscarVinculosPorPai(int $idAtivoPai): array
    {
        // JOIN em uma única query — evita o problema N+1 (RNF-008)
        $sql = "SELECT v.id, v.id_ativo_pai, v.id_ativo_filho, v.tipo_relacao, v.data_vinculo,
                       pai.nome AS nome_ativo_pai,
                       filho.nome AS nome_ativo_filho
                FROM vinculos v
                INNER JOIN ativos pai   ON pai.id   = v.id_ativo_pai
                INNER JOIN ativos filho ON filho.id = v.id_ativo_filho
                WHERE v.id_ativo_pai = :id_ativo_pai
                ORDER BY filho.nome ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute(['id_ativo_pai' => $idAtivoPai]);

        $resultado = [];

        while ($row = $stmt->fetch()) {
            $resultado[] = $this->mapearParaModel($row);
        }

        return $resultado;
    }

    // Retorna todos os vínculos indexados pelo id do ativo pai — usado no relatório para exibir dependências por ativo (RF-007)
    public function buscarTodosIndexadosPorPai(): array
    {
        $sql = "SELECT v.id_ativo_pai, v.tipo_relacao, filho.nome AS nome_ativo_filho
                FROM vinculos v
                INNER JOIN ativos filho ON filho.id = v.id_ativo_filho
                ORDER BY v.id_ativo_pai, filho.nome ASC";

        $stmt = $this->conexao->query($sql);

        $resultado = [];

        while ($row = $stmt->fetch()) {
            $idPai = (int) $row['id_ativo_pai'];
            $resultado[$idPai][] = $row['tipo_relacao'] . ': ' . $row['nome_ativo_filho'];
        }

        return $resultado; // Ex: [1 => ['Hospeda: Banco Oracle', 'Executa: Apache'], 2 => [...]]
    }

    // Verifica se já existe um vínculo entre dois ativos — usado pelo Service para garantir unicidade (Regra de Negócio seção 7)
    public function vinculoJaExiste(int $idAtivoPai, int $idAtivoFilho): bool
    {
        $sql = "SELECT COUNT(*) FROM vinculos
                WHERE id_ativo_pai = :id_ativo_pai AND id_ativo_filho = :id_ativo_filho";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([
            'id_ativo_pai'   => $idAtivoPai,
            'id_ativo_filho' => $idAtivoFilho,
        ]);

        return (int) $stmt->fetchColumn() > 0; // Retorna true se o vínculo já existir
    }

    // Remove um vínculo pelo id
    public function deletar(int $id): bool
    {
        $stmt = $this->conexao->prepare("DELETE FROM vinculos WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    // Converte uma linha do banco (array associativo) em um objeto VinculoModel — evita repetição de código
    private function mapearParaModel(array $row): VinculoModel
    {
        return new VinculoModel(
            id:           (int) $row['id'],
            idAtivoPai:   (int) $row['id_ativo_pai'],
            idAtivoFilho: (int) $row['id_ativo_filho'],
            tipoRelacao:  $row['tipo_relacao'],
            dataVinculo:  $row['data_vinculo'],
            nomePai:      $row['nome_ativo_pai']   ?? null, // Vem do JOIN — null se a query não trouxer o campo
            nomeFilho:    $row['nome_ativo_filho'] ?? null
        );
    }
}
