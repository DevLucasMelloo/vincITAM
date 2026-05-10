<?php
// View de criação de vínculo entre dois ativos. Exibe formulário com seleção de ativo pai, filho e tipo de relação conforme seção 6 do documento. Variáveis recebidas do VinculoController: $ativos e opcionalmente $erro.

/** @var \models\AtivoModel[] $ativos */
/** @var string|null $erro */
$ativos = $ativos ?? [];
$erro   = $erro   ?? null;

$tituloPagina = 'Novo Vínculo';
$paginaAtiva  = 'vinculos';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabeçalho da página -->
<div class="mb-4">
    <p class="text-muted small mb-1">ADMINISTRAÇÃO › DEPENDÊNCIAS › <a href="/vincitam/public/vinculos">VÍNCULOS</a></p>
    <h4 class="fw-bold mb-1">Criar Novo Vínculo</h4>
    <p class="text-muted small mb-0">Defina a relação hierárquica entre dois ativos de TI.</p>
</div>

<!-- Mensagem de erro de validação -->
<?php if (isset($erro)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="bg-white rounded-3 border p-4">
            <form method="POST" action="/vincitam/public/vinculos/gerar">

                <!-- Ativo Pai -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-diagram-3 text-primary me-1"></i>
                        Ativo Pai (Hospedeiro/Provedor) <span class="text-danger">*</span>
                    </label>
                    <select name="id_ativo_pai" class="form-select" required>
                        <option value="">Selecione o ativo pai...</option>
                        <?php foreach ($ativos as $ativo): ?>
                            <option value="<?= $ativo->id ?>"
                                <?= isset($_POST['id_ativo_pai']) && $_POST['id_ativo_pai'] == $ativo->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ativo->nome) ?> (<?= htmlspecialchars($ativo->tipo) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">O ativo que "sustenta" a relação. Ex: Servidor Físico.</div>
                </div>

                <!-- Seta indicativa -->
                <div class="text-center text-muted my-2">
                    <i class="bi bi-arrow-down-circle fs-4"></i>
                </div>

                <!-- Ativo Filho -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-diagram-2 text-success me-1"></i>
                        Ativo Filho (Dependente) <span class="text-danger">*</span>
                    </label>
                    <select name="id_ativo_filho" class="form-select" required>
                        <option value="">Selecione o ativo filho...</option>
                        <?php foreach ($ativos as $ativo): ?>
                            <option value="<?= $ativo->id ?>"
                                <?= isset($_POST['id_ativo_filho']) && $_POST['id_ativo_filho'] == $ativo->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ativo->nome) ?> (<?= htmlspecialchars($ativo->tipo) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">O ativo que "depende" ou está "dentro" do pai. Ex: Banco de Dados.</div>
                </div>

                <!-- Tipo de Relação -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipo de Relação <span class="text-danger">*</span></label>
                    <input type="text" name="tipo_relacao" class="form-control"
                           placeholder="Ex: Hospeda, Conectado via Fibra, Executa serviço de"
                           value="<?= htmlspecialchars($_POST['tipo_relacao'] ?? '') ?>"
                           list="sugestoes-relacao" required>
                    <!-- Sugestões de preenchimento conforme seção 6 do documento -->
                    <datalist id="sugestoes-relacao">
                        <option value="Hospeda">
                        <option value="Conectado via Fibra">
                        <option value="Executa serviço de">
                        <option value="Depende de">
                        <option value="Replica para">
                    </datalist>
                    <div class="form-text">Descreve a natureza do vínculo entre os dois ativos.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-check-circle me-1"></i> Criar Dependência
                    </button>
                    <a href="/vincitam/public/vinculos" class="btn btn-outline-secondary px-4">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
