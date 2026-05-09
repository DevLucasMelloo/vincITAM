<?php

// Camada de regras de negócio da entidade Ativo. Valida os dados recebidos do Controller antes de repassar ao DAO. O Controller nunca acessa o DAO diretamente — fluxo obrigatório: Controller -> Service -> DAO (seção 7 do documento).

declare(strict_types=1);

namespace services;

use dao\AtivoDAO;
use models\AtivoModel;

class AtivoService
{
    // Tipos de ativo permitidos conforme o documento (RF-002)
    private const TIPOS_VALIDOS = [
        'servidor',
        'banco_de_dados',
        'dispositivo_rede',
        'estacao_trabalho',
    ];

    // Status permitidos conforme o documento (RF-001)
    private const STATUS_VALIDOS = [
        'ativo',
        'manutencao',
        'desativado',
    ];

    // Recebe o DAO via construtor (injeção de dependência) — o Service não instancia o DAO, apenas o utiliza
    public function __construct(private readonly AtivoDAO $ativoDAO) {}

    // Valida e cadastra um novo ativo — retorna true em caso de sucesso ou lança exceção se inválido
    public function cadastrar(array $dados): bool
    {
        $ativo = $this->construirModel($dados);

        $this->validar($ativo);

        return $this->ativoDAO->salvar($ativo);
    }

    // Retorna todos os ativos cadastrados
    public function listarTodos(): array
    {
        return $this->ativoDAO->buscarTodos();
    }

    // Busca um ativo pelo id — lança exceção se não encontrado
    public function buscarPorId(int $id): AtivoModel
    {
        $ativo = $this->ativoDAO->buscarPorId($id);

        if ($ativo === null) {
            throw new \RuntimeException("Ativo com id {$id} não encontrado.");
        }

        return $ativo;
    }

    // Valida e atualiza um ativo existente
    public function atualizar(int $id, array $dados): bool
    {
        $this->buscarPorId($id); // Garante que o ativo existe antes de atualizar

        $ativo = $this->construirModel($dados, $id);

        $this->validar($ativo);

        return $this->ativoDAO->atualizar($ativo);
    }

    // Remove um ativo — os vínculos relacionados são removidos automaticamente via ON DELETE CASCADE (RNF-006)
    public function deletar(int $id): bool
    {
        $this->buscarPorId($id); // Garante que o ativo existe antes de deletar

        return $this->ativoDAO->deletar($id);
    }

    // Constrói um AtivoModel a partir dos dados recebidos do formulário (array $_POST)
    private function construirModel(array $dados, ?int $id = null): AtivoModel
    {
        return new AtivoModel(
            id:            $id,
            nome:          trim($dados['nome']          ?? ''),  // trim remove espaços extras do início e fim
            tipo:          trim($dados['tipo']          ?? ''),
            numeroSerie:   trim($dados['numero_serie']  ?? '') ?: null, // Converte string vazia em null (campo opcional)
            dataAquisicao: trim($dados['data_aquisicao']?? '') ?: null,
            status:        trim($dados['status']        ?? 'ativo'),
        );
    }

    // Valida as regras de negócio do ativo — lança exceção com mensagem clara se inválido
    private function validar(AtivoModel $ativo): void
    {
        if (empty($ativo->nome)) {
            throw new \InvalidArgumentException("O nome do ativo é obrigatório.");
        }

        if (!in_array($ativo->tipo, self::TIPOS_VALIDOS, strict: true)) {
            throw new \InvalidArgumentException("Tipo de ativo inválido: {$ativo->tipo}.");
        }

        if (!in_array($ativo->status, self::STATUS_VALIDOS, strict: true)) {
            throw new \InvalidArgumentException("Status inválido: {$ativo->status}.");
        }

        // Valida o formato da data se informada (AAAA-MM-DD conforme o documento seção 5)
        if ($ativo->dataAquisicao !== null) {
            $data = \DateTime::createFromFormat('Y-m-d', $ativo->dataAquisicao);
            if (!$data || $data->format('Y-m-d') !== $ativo->dataAquisicao) {
                throw new \InvalidArgumentException("Data de aquisição inválida. Use o formato AAAA-MM-DD.");
            }
        }
    }
}
