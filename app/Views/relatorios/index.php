<?php
$title = 'Relatórios';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-graph-up-arrow me-2"></i>Relatórios Gerenciais</h1>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/dashboard"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<ul class="list-group">
    <?php foreach ($relatorios as $r): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><?= htmlspecialchars((string) $r, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge text-bg-primary">Exportar PDF/Excel</span>
        </li>
    <?php endforeach; ?>
</ul>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
