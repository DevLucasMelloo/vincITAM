<?php
// View do Dashboard. Exibe visão geral do sistema com KPIs de ativos por tipo e total de vínculos. Variáveis recebidas do DashboardController: $totaisPorTipo, $totaisPorStatus, $totalAtivos, $totalVinculos.

/** @var array<string,int> $totaisPorTipo */
/** @var array<string,int> $totaisPorStatus */
/** @var int $totalAtivos */
/** @var int $totalVinculos */
$totaisPorTipo    = $totaisPorTipo    ?? [];
$totaisPorStatus  = $totaisPorStatus  ?? [];
$totalAtivos      = $totalAtivos      ?? 0;
$totalVinculos    = $totalVinculos    ?? 0;

$tituloPagina = 'Dashboard';
$paginaAtiva  = 'dashboard';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabeçalho da página -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <p class="text-muted small mb-1">ADMINISTRAÇÃO › VISÃO GERAL</p>
        <h4 class="fw-bold mb-1">Visão Geral de Infraestrutura</h4>
        <p class="text-muted small mb-0">Monitoramento consolidado dos ativos de TI cadastrados no sistema.</p>
    </div>
    <a href="/vincitam/public/ativos/novo" class="btn btn-dark d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i> Novo Ativo
    </a>
</div>

<!-- Cards KPI -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-hdd-stack text-primary fs-5"></i>
                <span class="kpi-label">Total de Ativos</span>
            </div>
            <div class="kpi-value"><?= (int) $totalAtivos ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-tools text-warning fs-5"></i>
                <span class="kpi-label">Em Manutenção</span>
            </div>
            <div class="kpi-value text-warning"><?= $totaisPorStatus['manutencao'] ?? 0 ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                <span class="kpi-label">Desativados</span>
            </div>
            <div class="kpi-value text-danger"><?= $totaisPorStatus['desativado'] ?? 0 ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-diagram-3 text-success fs-5"></i>
                <span class="kpi-label">Vínculos de Dependência</span>
            </div>
            <div class="kpi-value text-success"><?= (int) $totalVinculos ?></div>
        </div>
    </div>
</div>

<!-- Distribuição por categoria -->
<div class="row g-3">
    <div class="col-md-8">
        <div class="bg-white rounded-3 border p-4">
            <h6 class="fw-semibold mb-4">Distribuição por Categoria</h6>
            <div class="row g-3">
                <?php
                $categorias = [
                    'servidor'         => ['label' => 'Servidores',          'icon' => 'bi-server',        'cor' => 'primary'],
                    'banco_de_dados'   => ['label' => 'Bancos de Dados',     'icon' => 'bi-database',      'cor' => 'info'],
                    'dispositivo_rede' => ['label' => 'Dispositivos de Rede','icon' => 'bi-router',        'cor' => 'warning'],
                    'estacao_trabalho' => ['label' => 'Estações de Trabalho','icon' => 'bi-pc-display',    'cor' => 'success'],
                ];
                foreach ($categorias as $tipo => $info):
                    $total = $totaisPorTipo[$tipo] ?? 0;
                    $percentual = $totalAtivos > 0 ? round(($total / $totalAtivos) * 100) : 0;
                ?>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light">
                        <div class="rounded-3 p-2 bg-<?= $info['cor'] ?> bg-opacity-10">
                            <i class="bi <?= $info['icon'] ?> text-<?= $info['cor'] ?> fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold"><?= $info['label'] ?></span>
                                <span class="small text-muted"><?= $total ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px;">
                                <div class="progress-bar bg-<?= $info['cor'] ?>" style="width:<?= $percentual ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Ações rápidas -->
    <div class="col-md-4">
        <div class="bg-white rounded-3 border p-4 h-100">
            <h6 class="fw-semibold mb-4">Ações Rápidas</h6>
            <div class="d-flex flex-column gap-2">
                <a href="/vincitam/public/ativos/novo" class="btn btn-outline-primary text-start d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Cadastrar Novo Ativo
                </a>
                <a href="/vincitam/public/vinculos/novo" class="btn btn-outline-success text-start d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3"></i> Criar Novo Vínculo
                </a>
                <a href="/vincitam/public/ativos" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul"></i> Ver Inventário Completo
                </a>
                <a href="/vincitam/public/vinculos" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <i class="bi bi-share"></i> Ver Todos os Vínculos
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
