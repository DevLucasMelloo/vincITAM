<?php
// View de topologia de dependências de um ativo específico. Exibe o ativo selecionado e lista todos os ativos dependentes dele (RF-004). Variáveis recebidas do VinculoController: $ativo (AtivoModel), $vinculos (VinculoModel[]).

/** @var \models\AtivoModel $ativo */
/** @var \models\VinculoModel[] $vinculos */
$vinculos = $vinculos ?? [];

$tituloPagina = 'Topologia de Dependências';
$paginaAtiva  = 'vinculos';
require __DIR__ . '/../partials/header.php';

$labelTipo = [
    'servidor'          => 'Servidor',
    'banco_de_dados'    => 'Banco de Dados',
    'dispositivo_rede'  => 'Dispositivo de Rede',
    'estacao_trabalho'  => 'Estação de Trabalho',
];

$labelStatus = [
    'ativo'       => ['label' => 'Ativo',       'class' => 'badge-ativo'],
    'manutencao'  => ['label' => 'Manutenção',  'class' => 'badge-manutencao'],
    'desativado'  => ['label' => 'Desativado',  'class' => 'badge-desativado'],
];
?>

<!-- Cabeçalho da página -->
<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <p class="text-muted small mb-1">ADMINISTRAÇÃO › DEPENDÊNCIAS › <a href="/vincitam/public/vinculos">VÍNCULOS</a></p>
        <h4 class="fw-bold mb-1">Topologia de Dependências</h4>
        <p class="text-muted small mb-0">Ativos dependentes de <strong><?= htmlspecialchars($ativo->nome) ?></strong>.</p>
    </div>
    <a href="/vincitam/public/vinculos/novo" class="btn btn-dark d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i> Novo Vínculo
    </a>
</div>

<!-- Card do ativo pai (hospedeiro) -->
<div class="bg-dark text-white rounded-3 p-4 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 p-3 bg-white bg-opacity-10">
            <i class="bi bi-hdd-stack fs-3"></i>
        </div>
        <div>
            <p class="text-white-50 small mb-1">ATIVO PAI (HOSPEDEIRO)</p>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($ativo->nome) ?></h5>
            <div class="d-flex gap-3 small text-white-50">
                <span><i class="bi bi-tag me-1"></i><?= htmlspecialchars($labelTipo[$ativo->tipo] ?? $ativo->tipo) ?></span>
                <?php if ($ativo->numeroSerie): ?>
                    <span><i class="bi bi-upc me-1"></i><?= htmlspecialchars($ativo->numeroSerie) ?></span>
                <?php endif; ?>
                <span>
                    <span class="badge rounded-pill px-2 py-1 <?= $labelStatus[$ativo->status]['class'] ?? '' ?>">
                        <?= htmlspecialchars($labelStatus[$ativo->status]['label'] ?? $ativo->status) ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="ms-auto text-end">
            <p class="text-white-50 small mb-1">DEPENDENTES DIRETOS</p>
            <h3 class="fw-bold mb-0"><?= count($vinculos) ?></h3>
        </div>
    </div>
</div>

<!-- Indicador de hierarquia -->
<?php if (!empty($vinculos)): ?>
<div class="text-center text-muted mb-3">
    <i class="bi bi-arrow-down-circle fs-4"></i>
    <p class="small mb-0">Ativos que dependem ou estão hospedados em <strong><?= htmlspecialchars($ativo->nome) ?></strong></p>
</div>
<?php endif; ?>

<!-- Tabela de dependências -->
<div class="bg-white rounded-3 border">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold mb-0">Ativos Dependentes</h6>
        <span class="badge bg-secondary"><?= count($vinculos) ?> dependente(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 sortable-table">
            <thead class="table-dark">
                <tr>
                    <th class="ps-4">ATIVO FILHO (DEPENDENTE)</th>
                    <th>TIPO DE RELAÇÃO</th>
                    <th>DATA DO VÍNCULO</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vinculos)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-diagram-3 fs-2 d-block mb-2"></i>
                            Nenhum ativo dependente encontrado para <strong><?= htmlspecialchars($ativo->nome) ?></strong>.
                            <br>
                            <a href="/vincitam/public/vinculos/novo" class="btn btn-sm btn-dark mt-3">
                                <i class="bi bi-plus-circle me-1"></i> Criar Primeiro Vínculo
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vinculos as $vinculo): ?>
                    <tr>
                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($vinculo->nomeFilho ?? "ID {$vinculo->idAtivoFilho}") ?></td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">
                                <?= htmlspecialchars($vinculo->tipoRelacao) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= $vinculo->dataVinculo ? date('d/m/Y H:i', strtotime($vinculo->dataVinculo)) : '—' ?></td>
                        <td class="text-center">
                            <form method="POST" action="/vincitam/public/vinculos/deletar"
                                  onsubmit="return confirm('Deseja remover este vínculo?')">
                                <input type="hidden" name="id" value="<?= $vinculo->id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($vinculos)): ?>
    <div class="p-3 border-top d-flex gap-2">
        <a href="/vincitam/public/ativos/editar?id=<?= $ativo->id ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Editar Ativo
        </a>
        <a href="/vincitam/public/vinculos" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list me-1"></i> Ver Todos os Vínculos
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
