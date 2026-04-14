<?php
$title = 'Mesas e Pedidos';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 module-title"><i class="bi bi-grid-3x3-gap me-2"></i>Mesas e Pedidos</h1>
    <a href="/mesas/cardapio-digital" class="btn btn-outline-primary btn-sm"><i class="bi bi-qr-code-scan me-1"></i>Cardápio Digital</a>
</div>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/dashboard"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<input type="text" class="form-control mb-3" placeholder="Filtrar ao digitar..." data-filter-input="#tabela-mesas">
<table class="table table-striped" id="tabela-mesas" data-filter-table>
    <thead><tr><th>Mesa</th><th>Pedido</th><th>Status</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= htmlspecialchars((string) $r['mesa'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['pedido'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['status'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>R$ <?= number_format((float) $r['total'], 2, ',', '.') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
