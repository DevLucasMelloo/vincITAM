<?php
// View de listagem de ativos. Exibe todos os ativos cadastrados em tabela com filtros rápidos por tipo e ações de editar/excluir. Variável recebida do AtivoController: $ativos.

/** @var \models\AtivoModel[] $ativos */
$ativos = $ativos ?? [];

$tituloPagina = 'Inventário de Ativos';
$paginaAtiva  = 'ativos';
require __DIR__ . '/../partials/header.php';

// Mapeia os labels de tipo e status para exibição amigável
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
        <p class="text-muted small mb-1">ADMINISTRAÇÃO › INVENTÁRIO</p>
        <h4 class="fw-bold mb-1">Inventário de Ativos</h4>
        <p class="text-muted small mb-0">Gestão integral de hardware e software cadastrados no sistema.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/vincitam/public/relatorios" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Exportar Lista
        </a>
        <a href="/vincitam/public/ativos/novo" class="btn btn-dark d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Novo Ativo
        </a>
    </div>
</div>

<!-- Mensagens de sucesso/erro -->
<?php if (isset($_GET['sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <?= match($_GET['sucesso']) {
            'cadastro' => 'Ativo cadastrado com sucesso!',
            'edicao'   => 'Ativo atualizado com sucesso!',
            'exclusao' => 'Ativo removido com sucesso!',
            default    => 'Operação realizada com sucesso!'
        } ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Ativo não encontrado.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filtros de busca (RF-006) -->
<div class="bg-white rounded-3 border p-3 mb-4">
    <form method="GET" action="/vincitam/public/ativos" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-semibold mb-1">BUSCAR</label>
            <input type="text" name="busca" class="form-control form-control-sm"
                   placeholder="Nome do ativo ou número de série..."
                   value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">TIPO</label>
            <select name="tipo" class="form-select form-select-sm">
                <option value="">Todos os Tipos</option>
                <option value="servidor"         <?= ($_GET['tipo'] ?? '') === 'servidor'         ? 'selected' : '' ?>>Servidor</option>
                <option value="banco_de_dados"   <?= ($_GET['tipo'] ?? '') === 'banco_de_dados'   ? 'selected' : '' ?>>Banco de Dados</option>
                <option value="dispositivo_rede" <?= ($_GET['tipo'] ?? '') === 'dispositivo_rede' ? 'selected' : '' ?>>Dispositivo de Rede</option>
                <option value="estacao_trabalho" <?= ($_GET['tipo'] ?? '') === 'estacao_trabalho' ? 'selected' : '' ?>>Estação de Trabalho</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold mb-1">STATUS</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="ativo"      <?= ($_GET['status'] ?? '') === 'ativo'      ? 'selected' : '' ?>>Ativo</option>
                <option value="manutencao" <?= ($_GET['status'] ?? '') === 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
                <option value="desativado" <?= ($_GET['status'] ?? '') === 'desativado' ? 'selected' : '' ?>>Desativado</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Filtrar</button>
            <?php if (!empty($_GET['busca']) || !empty($_GET['tipo']) || !empty($_GET['status'])): ?>
                <a href="/vincitam/public/ativos" class="btn btn-outline-secondary btn-sm" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabela de ativos -->
<div class="bg-white rounded-3 border">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold mb-0">Inventário de Ativos</h6>
        <span class="badge bg-secondary"><?= count($ativos) ?> registro(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 sortable-table">
            <thead class="table-dark">
                <tr>
                    <th class="ps-4">ATIVO / NOME</th>
                    <th>TIPO</th>
                    <th>Nº DE SÉRIE</th>
                    <th>AQUISIÇÃO</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ativos)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Nenhum ativo cadastrado. <a href="/vincitam/public/ativos/novo">Cadastrar agora</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ativos as $ativo): ?>
                    <tr>
                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($ativo->nome) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($labelTipo[$ativo->tipo] ?? $ativo->tipo) ?></td>
                        <td class="text-muted font-monospace small"><?= htmlspecialchars($ativo->numeroSerie ?? '—') ?></td>
                        <td class="text-muted"><?= $ativo->dataAquisicao ? date('d/m/Y', strtotime($ativo->dataAquisicao)) : '—' ?></td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 <?= $labelStatus[$ativo->status]['class'] ?? '' ?>">
                                <?= htmlspecialchars($labelStatus[$ativo->status]['label'] ?? $ativo->status) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary rounded-2" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="/vincitam/public/ativos/editar?id=<?= $ativo->id ?>">
                                            <i class="bi bi-pencil text-primary"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="/vincitam/public/vinculos/topologia?id=<?= $ativo->id ?>">
                                            <i class="bi bi-diagram-3 text-success"></i> Ver Dependências
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="/vincitam/public/ativos/deletar" onsubmit="return confirm('Deseja remover este ativo? Os vínculos relacionados também serão removidos.')">
                                            <input type="hidden" name="id" value="<?= $ativo->id ?>">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                <i class="bi bi-trash"></i> Remover
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Rodapé da tabela -->
    <div class="p-3 border-top text-muted small">
        Exibindo <?= count($ativos) ?> ativo(s)
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
