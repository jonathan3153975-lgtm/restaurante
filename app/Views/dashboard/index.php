<?php
$title = 'Dashboard';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Painel Operacional</h1>
        <p class="text-muted mb-0">Bem-vindo, <?= htmlspecialchars((string)($user['name'] ?? 'Usuário'), ENT_QUOTES, 'UTF-8') ?>.</p>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-3">
        <a class="card card-link p-3" href="/admin/notas-entrada">
            <i class="bi bi-receipt-cutoff fs-4 mb-2"></i>
            <strong>Notas de Entrada</strong>
            <span>Administrativo</span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="card card-link p-3" href="/estoque">
            <i class="bi bi-box-seam fs-4 mb-2"></i>
            <strong>Estoque</strong>
            <span>Controle de produtos</span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="card card-link p-3" href="/financeiro">
            <i class="bi bi-cash-stack fs-4 mb-2"></i>
            <strong>Financeiro</strong>
            <span>Caixa e fluxo</span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="card card-link p-3" href="/mesas">
            <i class="bi bi-grid-3x3-gap fs-4 mb-2"></i>
            <strong>Mesas e Pedidos</strong>
            <span>Operação de salão</span>
        </a>
    </div>
</div>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
