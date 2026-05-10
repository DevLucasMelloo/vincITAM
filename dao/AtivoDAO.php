<?php

// Camada de acesso a dados da entidade Ativo. Contém todo o SQL da tabela 'ativos' e é a única camada que se comunica diretamente com o banco via PDO.

declare(strict_types=1);

namespace dao;

use models\AtivoModel;
use PDO;

class AtivoDAO
{
    // Recebe a conexão PDO via construtor (injeção de dependência) — o DAO não cria a conexão, apenas a utiliza
    public function __construct(private readonly PDO $conexao) {}

    // Insere um novo ativo no banco e retorna true em caso de sucesso
    public function salvar(AtivoModel $ativo): bool
    {
        $sql = "INSERT INTO ativos (nome, tipo, numero_serie, data_aquisicao, status)
                VALUES (:nome, :tipo, :numero_serie, :data_aquisicao, :status)";

        $stmt = $this->conexao->prepare($sql); // Prepared Statement — protege contra SQL Injection (RNF-002)

        return $stmt->execute([
            'nome'           => $ativo->nome,
            'tipo'           => $ativo->tipo,
            'numero_serie'   => $ativo->numeroSerie,   // camelCase no PHP → snake_case no banco (RNF-007)
            'data_aquisicao' => $ativo->dataAquisicao,
            'status'         => $ativo->status,
        ]);
    }

    // Retorna todos os ativos ordenados pelo nome, com filtros opcionais por tipo, status e busca textual por nome ou número de série (RF-006, RF-007)
    public function buscarTodos(?string $tipo = null, ?string $status = null, ?string $busca = null): array
    {
        $where  = [];
        $params = [];

        if ($tipo !== null) {
            $where[]        = 'tipo = :tipo';
            $params['tipo'] = $tipo;
        }

        if ($status !== null) {
            $where[]          = 'status = :status';
            $params['status'] = $status;
        }

        if ($busca !== null) {
            // LIKE com % nos dois lados — busca por nome OU número de série (RF-006)
            $where[]          = '(nome LIKE :busca OR numero_serie LIKE :busca)';
            $params['busca']  = '%' . $busca . '%';
        }

        $sql  = 'SELECT * FROM ativos';
        $sql .= $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $sql .= ' ORDER BY nome ASC';

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute($params);

        $resultado = [];
        while ($row = $stmt->fetch()) {
            $resultado[] = $this->mapearParaModel($row);
        }

        return $resultado;
    }

    // Busca um ativo pelo id — retorna AtivoModel ou null se não encontrado
    public function buscarPorId(int $id): ?AtivoModel
    {
        $stmt = $this->conexao->prepare("SELECT * FROM ativos WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ? $this->mapearParaModel($row) : null; // Retorna null se o ativo não existir
    }

    // Busca ativos pelo tipo — usado para validação de hierarquia no Service (RF-003)
    public function buscarPorTipo(string $tipo): array
    {
        $stmt = $this->conexao->prepare("SELECT * FROM ativos WHERE tipo = :tipo ORDER BY nome ASC");
        $stmt->execute(['tipo' => $tipo]);

        $resultado = [];

        while ($row = $stmt->fetch()) {
            $resultado[] = $this->mapearParaModel($row);
        }

        return $resultado;
    }

    // Atualiza os dados de um ativo existente no banco
    public function atualizar(AtivoModel $ativo): bool
    {
        $sql = "UPDATE ativos
                SET nome = :nome, tipo = :tipo, numero_serie = :numero_serie,
                    data_aquisicao = :data_aquisicao, status = :status
                WHERE id = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            'id'             => $ativo->id,
            'nome'           => $ativo->nome,
            'tipo'           => $ativo->tipo,
            'numero_serie'   => $ativo->numeroSerie,
            'data_aquisicao' => $ativo->dataAquisicao,
            'status'         => $ativo->status,
        ]);
    }

    // Remove um ativo do banco — os vínculos relacionados são removidos automaticamente via ON DELETE CASCADE (RNF-006)
    public function deletar(int $id): bool
    {
        $stmt = $this->conexao->prepare("DELETE FROM ativos WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    // Converte uma linha do banco (array associativo) em um objeto AtivoModel — evita repetição de código
    private function mapearParaModel(array $row): AtivoModel
    {
        return new AtivoModel(
            id:            (int) $row['id'],
            nome:          $row['nome'],
            tipo:          $row['tipo'],
            numeroSerie:   $row['numero_serie'],   // snake_case do banco → camelCase no PHP (RNF-007)
            dataAquisicao: $row['data_aquisicao'],
            status:        $row['status']
        );
    }
}
