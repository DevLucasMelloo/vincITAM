<?php
// View de relatórios gerenciais. Exibe listagem filtrável de ativos com opções de exportação CSV (RF-007). Variável recebida do RelatorioController: $ativos.

/** @var \models\AtivoModel[] $ativos */
$ativos = $ativos ?? [];

$tituloPagina = 'Relatórios Gerenciais';
$paginaAtiva  = 'relatorios';
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
        <p class="text-muted small mb-1">ADMINISTRAÇÃO › RELATÓRIOS</p>
        <h4 class="fw-bold mb-1">Relatórios Gerenciais</h4>
        <p class="text-muted small mb-0">Gere inventários detalhados e análises de conformidade da infraestrutura.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/vincitam/public/relatorios/exportar-csv" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Exportar CSV
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-3 border p-4 mb-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-funnel me-1"></i> Parâmetros do Relatório</h6>
    <form method="GET" action="/vincitam/public/relatorios" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">TIPO DE ATIVO</label>
            <select name="tipo" class="form-select form-select-sm">
                <option value="">Todos os Ativos</option>
                <option value="servidor"         <?= ($_GET['tipo'] ?? '') === 'servidor'         ? 'selected' : '' ?>>Servidor</option>
                <option value="banco_de_dados"   <?= ($_GET['tipo'] ?? '') === 'banco_de_dados'   ? 'selected' : '' ?>>Banco de Dados</option>
                <option value="dispositivo_rede" <?= ($_GET['tipo'] ?? '') === 'dispositivo_rede' ? 'selected' : '' ?>>Dispositivo de Rede</option>
                <option value="estacao_trabalho" <?= ($_GET['tipo'] ?? '') === 'estacao_trabalho' ? 'selected' : '' ?>>Estação de Trabalho</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">STATUS</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos os Status</option>
                <option value="ativo"      <?= ($_GET['status'] ?? '') === 'ativo'      ? 'selected' : '' ?>>Ativo</option>
                <option value="manutencao" <?= ($_GET['status'] ?? '') === 'manutencao' ? 'selected' : '' ?>>Em Manutenção</option>
                <option value="desativado" <?= ($_GET['status'] ?? '') === 'desativado' ? 'selected' : '' ?>>Desativado</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filtrar</button>
        </div>
    </form>
</div>

<!-- Tabela de resultados -->
<div class="bg-white rounded-3 border">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-semibold mb-0">Pré-visualização dos Dados</h6>
        <span class="badge bg-secondary"><?= count($ativos) ?> registro(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 sortable-table">
            <thead class="table-dark">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>ATIVO</th>
                    <th>CATEGORIA</th>
                    <th>Nº SÉRIE</th>
                    <th>AQUISIÇÃO</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ativos)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Nenhum ativo encontrado com os filtros selecionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ativos as $ativo): // Filtros já aplicados server-side pelo Controller ?>
                    <tr>
                        <td class="ps-4 text-muted font-monospace small"><?= $ativo->id ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($ativo->nome) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($labelTipo[$ativo->tipo] ?? $ativo->tipo) ?></td>
                        <td class="text-muted font-monospace small"><?= htmlspecialchars($ativo->numeroSerie ?? '—') ?></td>
                        <td class="text-muted"><?= $ativo->dataAquisicao ? date('d/m/Y', strtotime($ativo->dataAquisicao)) : '—' ?></td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 <?= $labelStatus[$ativo->status]['class'] ?? '' ?>">
                                <?= htmlspecialchars($labelStatus[$ativo->status]['label'] ?? $ativo->status) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
