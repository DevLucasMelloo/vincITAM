<?php
// View de edição de ativo existente. Exibe formulário pré-preenchido com os dados atuais. Variáveis recebidas do AtivoController: $ativo (AtivoModel) e opcionalmente $erro.

/** @var \models\AtivoModel $ativo */
/** @var string|null $erro */
$erro = $erro ?? null;

$tituloPagina = 'Editar Ativo';
$paginaAtiva  = 'ativos';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabeçalho da página -->
<div class="mb-4">
    <p class="text-muted small mb-1">ADMINISTRAÇÃO › INVENTÁRIO › <a href="/vincitam/public/ativos">ATIVOS</a></p>
    <h4 class="fw-bold mb-1">Editar Ativo</h4>
    <p class="text-muted small mb-0">Atualize os dados do ativo <strong><?= htmlspecialchars($ativo->nome) ?></strong>.</p>
</div>

<!-- Mensagem de erro de validação -->
<?php if (isset($erro)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <div class="bg-white rounded-3 border p-4">
            <form method="POST" action="/vincitam/public/ativos/atualizar">
                <!-- ID oculto para identificar qual ativo está sendo editado -->
                <input type="hidden" name="id" value="<?= $ativo->id ?>">

                <!-- Nome do Ativo -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome do Ativo <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control"
                           value="<?= htmlspecialchars($ativo->nome) ?>" required>
                </div>

                <!-- Tipo de Ativo -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de Ativo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select" required>
                        <option value="servidor"         <?= $ativo->tipo === 'servidor'         ? 'selected' : '' ?>>Servidor</option>
                        <option value="banco_de_dados"   <?= $ativo->tipo === 'banco_de_dados'   ? 'selected' : '' ?>>Banco de Dados</option>
                        <option value="dispositivo_rede" <?= $ativo->tipo === 'dispositivo_rede' ? 'selected' : '' ?>>Dispositivo de Rede</option>
                        <option value="estacao_trabalho" <?= $ativo->tipo === 'estacao_trabalho' ? 'selected' : '' ?>>Estação de Trabalho</option>
                    </select>
                </div>

                <div class="row">
                    <!-- Número de Série -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Número de Série</label>
                        <input type="text" name="numero_serie" class="form-control font-monospace"
                               value="<?= htmlspecialchars($ativo->numeroSerie ?? '') ?>">
                    </div>

                    <!-- Data de Aquisição -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Aquisição</label>
                        <input type="date" name="data_aquisicao" class="form-control"
                               value="<?= htmlspecialchars($ativo->dataAquisicao ?? '') ?>">
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="ativo"      <?= $ativo->status === 'ativo'      ? 'selected' : '' ?>>Ativo</option>
                        <option value="manutencao" <?= $ativo->status === 'manutencao' ? 'selected' : '' ?>>Em Manutenção</option>
                        <option value="desativado" <?= $ativo->status === 'desativado' ? 'selected' : '' ?>>Desativado</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-check-circle me-1"></i> Salvar Alterações
                    </button>
                    <a href="/vincitam/public/ativos" class="btn btn-outline-secondary px-4">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
