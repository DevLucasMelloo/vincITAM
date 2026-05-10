<?php

// Camada de regras de negócio da entidade Vinculo. Valida as relações entre ativos antes de repassar ao DAO, aplicando as regras da seção 7 do documento: impedimento de auto-vínculo, validação de hierarquia e unicidade.

declare(strict_types=1);

namespace services;

use dao\VinculoDAO;
use dao\AtivoDAO;
use models\VinculoModel;

class VinculoService
{
    // Hierarquias inválidas — define quais combinações pai/filho representam uma hierarquia invertida (seção 7 do documento)
    private const HIERARQUIAS_INVALIDAS = [
        'banco_de_dados'   => ['servidor', 'dispositivo_rede', 'estacao_trabalho'], // Banco não pode hospedar esses tipos
        'dispositivo_rede' => ['servidor'],                                          // Dispositivo de rede não pode hospedar servidor
    ];

    // Recebe os DAOs via construtor (injeção de dependência) — o Service não instancia DAOs, apenas os utiliza
    public function __construct(
        private readonly VinculoDAO $vinculoDAO,
        private readonly AtivoDAO   $ativoDAO    // Necessário para buscar os tipos dos ativos e validar hierarquia
    ) {}

    // Valida e cria um novo vínculo entre dois ativos
    public function vincular(array $dados): bool
    {
        $idAtivoPai   = (int) ($dados['id_ativo_pai']   ?? 0);
        $idAtivoFilho = (int) ($dados['id_ativo_filho'] ?? 0);
        $tipoRelacao  = trim($dados['tipo_relacao']     ?? '');

        $this->validar($idAtivoPai, $idAtivoFilho, $tipoRelacao);

        $vinculo = new VinculoModel(
            idAtivoPai:   $idAtivoPai,
            idAtivoFilho: $idAtivoFilho,
            tipoRelacao:  $tipoRelacao,
        );

        return $this->vinculoDAO->salvar($vinculo);
    }

    // Retorna todos os vínculos com os nomes dos ativos pai e filho
    public function listarTodos(): array
    {
        return $this->vinculoDAO->buscarTodos();
    }

    // Retorna todos os vínculos de um ativo pai — exibe o que está hospedado/dependente dele (RF-004)
    public function obterTopologia(int $idAtivoPai): array
    {
        $ativo = $this->ativoDAO->buscarPorId($idAtivoPai);

        if ($ativo === null) {
            throw new \RuntimeException("Ativo com id {$idAtivoPai} não encontrado.");
        }

        return $this->vinculoDAO->buscarVinculosPorPai($idAtivoPai);
    }

    // Retorna todos os vínculos indexados pelo id do ativo pai — usado no relatório CSV (RF-007)
    public function listarIndexadosPorPai(): array
    {
        return $this->vinculoDAO->buscarTodosIndexadosPorPai();
    }

    // Remove um vínculo pelo id
    public function deletar(int $id): bool
    {
        return $this->vinculoDAO->deletar($id);
    }

    // Aplica todas as regras de negócio da seção 7 do documento antes de salvar o vínculo
    private function validar(int $idAtivoPai, int $idAtivoFilho, string $tipoRelacao): void
    {
        // Regra 1 — Impedimento de auto-vínculo: um ativo não pode ser pai de si mesmo
        if ($idAtivoPai === $idAtivoFilho) {
            throw new \InvalidArgumentException("Um ativo não pode ser vinculado a si mesmo.");
        }

        if (empty($tipoRelacao)) {
            throw new \InvalidArgumentException("O tipo de relação é obrigatório.");
        }

        // Verifica se os ativos existem antes de criar o vínculo
        $ativoPai   = $this->ativoDAO->buscarPorId($idAtivoPai);
        $ativoFilho = $this->ativoDAO->buscarPorId($idAtivoFilho);

        if ($ativoPai === null) {
            throw new \RuntimeException("Ativo pai com id {$idAtivoPai} não encontrado.");
        }

        if ($ativoFilho === null) {
            throw new \RuntimeException("Ativo filho com id {$idAtivoFilho} não encontrado.");
        }

        // Regra 2 — Validação de hierarquia: alerta se a combinação pai/filho parecer invertida
        if (isset(self::HIERARQUIAS_INVALIDAS[$ativoPai->tipo])) {
            if (in_array($ativoFilho->tipo, self::HIERARQUIAS_INVALIDAS[$ativoPai->tipo], strict: true)) {
                throw new \InvalidArgumentException(
                    "Hierarquia possivelmente invertida: um '{$ativoPai->tipo}' não deveria hospedar um '{$ativoFilho->tipo}'. Verifique se pai e filho estão corretos."
                );
            }
        }

        // Regra 3 — Unicidade: não permite vínculos duplicados para a mesma relação pai-filho
        if ($this->vinculoDAO->vinculoJaExiste($idAtivoPai, $idAtivoFilho)) {
            throw new \InvalidArgumentException("Já existe um vínculo entre esses dois ativos.");
        }
    }
}
