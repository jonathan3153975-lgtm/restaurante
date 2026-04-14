<?php
$title = 'Cardápio Digital';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-qr-code-scan me-2"></i>Cardápio Digital (QR-Code)</h1>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/mesas"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<p class="text-muted">Tela pública para mesa, sem login.</p>
<div class="row g-3">
    <?php foreach ($itens as $item): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6"><?= htmlspecialchars((string) $item['item'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="mb-1 text-muted"><?= htmlspecialchars((string) $item['categoria'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="fw-bold mb-0">R$ <?= number_format((float) $item['preco'], 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
