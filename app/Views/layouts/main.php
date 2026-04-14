<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Restaurante SaaS', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app-shell">
<?php
$menuItems = [
    ['/dashboard', 'Dashboard', 'bi-speedometer2'],
    ['/admin/notas-entrada', 'Administrativo', 'bi-receipt-cutoff'],
    ['/estoque', 'Estoque', 'bi-box-seam'],
    ['/rh', 'RH', 'bi-people'],
    ['/financeiro', 'Financeiro', 'bi-cash-stack'],
    ['/cardapio', 'Cardápio', 'bi-journal-richtext'],
    ['/mesas', 'Mesas e Pedidos', 'bi-grid-3x3-gap'],
    ['/cozinha', 'Cozinha', 'bi-fire'],
    ['/fiscal', 'Fiscal', 'bi-shield-check'],
    ['/relatorios', 'Relatórios', 'bi-graph-up-arrow'],
];
?>

<div class="d-lg-none topbar-mobile d-flex align-items-center justify-content-between px-3 py-2">
    <button class="btn btn-outline-gold btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="bi bi-list"></i>
    </button>
    <a class="brand-title" href="/dashboard">Restaurante SaaS</a>
    <form method="post" action="/logout" class="m-0">
        <button class="btn btn-gold btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i></button>
    </form>
</div>

<div class="offcanvas offcanvas-start sidebar-mobile" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-secondary-subtle">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="nav flex-column sidebar-nav p-3">
            <?php foreach ($menuItems as [$path, $label, $icon]): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?> me-2"></i>
                        <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="app-layout">
    <aside class="sidebar-desktop d-none d-lg-flex flex-column">
        <div class="sidebar-brand px-3 py-4">
            <span class="brand-dot"></span>
            <a class="brand-title" href="/dashboard">Restaurante SaaS</a>
        </div>
        <ul class="nav flex-column sidebar-nav px-3">
            <?php foreach ($menuItems as [$path, $label, $icon]): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?> me-2"></i>
                        <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-auto p-3">
            <form method="post" action="/logout">
                <button class="btn btn-gold w-100" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Sair</button>
            </form>
        </div>
    </aside>

    <div class="content-wrap w-100">
        <header class="content-top d-none d-lg-flex align-items-center justify-content-between">
            <h1 class="content-title mb-0"><?= htmlspecialchars($title ?? 'Painel', ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="top-chip"><i class="bi bi-circle-fill me-2"></i>Online</div>
        </header>
        <main class="content-main py-4 px-3 px-lg-4">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script src="/assets/js/masks.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
