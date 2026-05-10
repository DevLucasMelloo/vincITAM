<?php
// View de cadastro de novo ativo. Exibe formulário com os campos definidos na seção 5 do documento. Variável opcional recebida do AtivoController em caso de erro: $erro.

$tituloPagina = 'Novo Ativo';
$paginaAtiva  = 'ativos';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabeçalho da página -->
<div class="mb-4">
    <p class="text-muted small mb-1">ADMINISTRAÇÃO › INVENTÁRIO › <a href="/vincitam/public/ativos">ATIVOS</a></p>
    <h4 class="fw-bold mb-1">Cadastrar Novo Ativo</h4>
    <p class="text-muted small mb-0">Preencha os dados do ativo de TI a ser registrado no sistema.</p>
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
            <form method="POST" action="/vincitam/public/ativos/salvar">

                <!-- Nome do Ativo (obrigatório) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome do Ativo <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control"
                           placeholder="Ex: Servidor HP-PROD-01"
                           value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                           required>
                    <div class="form-text">Nome identificador único do ativo no sistema.</div>
                </div>

                <!-- Tipo de Ativo (obrigatório) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de Ativo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Selecione o tipo...</option>
                        <option value="servidor"          <?= ($_POST['tipo'] ?? '') === 'servidor'          ? 'selected' : '' ?>>Servidor</option>
                        <option value="banco_de_dados"    <?= ($_POST['tipo'] ?? '') === 'banco_de_dados'    ? 'selected' : '' ?>>Banco de Dados</option>
                        <option value="dispositivo_rede"  <?= ($_POST['tipo'] ?? '') === 'dispositivo_rede'  ? 'selected' : '' ?>>Dispositivo de Rede</option>
                        <option value="estacao_trabalho"  <?= ($_POST['tipo'] ?? '') === 'estacao_trabalho'  ? 'selected' : '' ?>>Estação de Trabalho</option>
                    </select>
                </div>

                <div class="row">
                    <!-- Número de Série (opcional) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Número de Série</label>
                        <input type="text" name="numero_serie" class="form-control font-monospace"
                               placeholder="Ex: SN-12345-XYZ"
                               value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>">
                        <div class="form-text">Opcional — identificação física do hardware.</div>
                    </div>

                    <!-- Data de Aquisição (opcional) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Aquisição</label>
                        <input type="date" name="data_aquisicao" class="form-control"
                               value="<?= htmlspecialchars($_POST['data_aquisicao'] ?? '') ?>">
                        <div class="form-text">Opcional — para controle de garantia.</div>
                    </div>
                </div>

                <!-- Status (obrigatório) -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="ativo"      <?= ($_POST['status'] ?? 'ativo') === 'ativo'      ? 'selected' : '' ?>>Ativo</option>
                        <option value="manutencao" <?= ($_POST['status'] ?? '') === 'manutencao' ? 'selected' : '' ?>>Em Manutenção</option>
                        <option value="desativado" <?= ($_POST['status'] ?? '') === 'desativado' ? 'selected' : '' ?>>Desativado</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-check-circle me-1"></i> Cadastrar Ativo
                    </button>
                    <a href="/vincitam/public/ativos" class="btn btn-outline-secondary px-4">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
