<?php
// View de mapeamento de dependências. Lista todos os vínculos e exibe formulário lateral para criar novo vínculo. Variáveis recebidas do VinculoController: $vinculos, $ativos.

/** @var \models\VinculoModel[] $vinculos */
/** @var \models\AtivoModel[] $ativos */
$vinculos = $vinculos ?? [];
$ativos   = $ativos   ?? [];

$tituloPagina = 'Mapeamento de Dependências';
$paginaAtiva  = 'vinculos';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabeçalho da página -->
<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <p class="text-muted small mb-1">ADMINISTRAÇÃO › DEPENDÊNCIAS</p>
        <h4 class="fw-bold mb-1">Mapeamento de Dependências</h4>
        <p class="text-muted small mb-0">Visualize e gerencie as relações entre os ativos de TI.</p>
    </div>
    <a href="/vincitam/public/vinculos/novo" class="btn btn-dark d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i> Novo Vínculo
    </a>
</div>

<!-- Mensagens de sucesso -->
<?php if (isset($_GET['sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <?= match($_GET['sucesso']) {
            'vinculo' => 'Vínculo criado com sucesso!',
            'exclusao' => 'Vínculo removido com sucesso!',
            default    => 'Operação realizada com sucesso!'
        } ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <!-- Tabela de vínculos -->
    <div class="col-md-8">
        <div class="bg-white rounded-3 border">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0">Análise de Dependências</h6>
                <span class="badge bg-secondary"><?= count($vinculos) ?> vínculo(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 sortable-table">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">ATIVO PAI (HOSPEDEIRO)</th>
                            <th>TIPO DE RELAÇÃO</th>
                            <th>ATIVO FILHO (DEPENDENTE)</th>
                            <th>DATA DO VÍNCULO</th>
                            <th class="text-center">AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vinculos)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-diagram-3 fs-2 d-block mb-2"></i>
                                    Nenhum vínculo cadastrado. <a href="/vincitam/public/vinculos/novo">Criar agora</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vinculos as $vinculo): ?>
                            <tr>
                                <td class="ps-4 fw-semibold"><?= htmlspecialchars($vinculo->nomePai ?? "ID {$vinculo->idAtivoPai}") ?></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">
                                        <?= htmlspecialchars($vinculo->tipoRelacao) ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($vinculo->nomeFilho ?? "ID {$vinculo->idAtivoFilho}") ?></td>
                                <td class="text-muted small"><?= $vinculo->dataVinculo ? date('d/m/Y', strtotime($vinculo->dataVinculo)) : '—' ?></td>
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
        </div>
    </div>

    <!-- Card de saúde da rede -->
    <div class="col-md-4">
        <div class="bg-dark text-white rounded-3 p-4">
            <h6 class="fw-semibold mb-3">Saúde da Rede</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-white-50">Total de Dependências</span>
                <span class="fw-bold"><?= count($vinculos) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="small text-white-50">Total de Ativos</span>
                <span class="fw-bold"><?= count($ativos) ?></span>
            </div>
            <hr class="border-secondary">
            <a href="/vincitam/public/vinculos/novo" class="btn btn-primary w-100 mt-2">
                <i class="bi bi-plus-circle me-1"></i> Criar Dependência
            </a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
