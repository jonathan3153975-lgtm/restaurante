<?php

$user = current_user();
$appName = app_config('app.name', 'Tech-Food');
$flashMessages = flash();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(App\Core\Csrf::token()) ?>">
    <title><?= e($appName) ?></title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="<?= e(asset('assets/js/app.js')) ?>"></script>
</head>
<body>
    <div class="shell">
        <?php require App\Core\App::basePath('views/partials/sidebar.php'); ?>
        <button type="button" class="sidebar-backdrop" data-sidebar-backdrop aria-label="Fechar menu"></button>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-heading">
                    <?php if (is_array($user)): ?>
                        <button
                            type="button"
                            class="button button-ghost sidebar-toggle"
                            data-sidebar-toggle
                            aria-controls="app-sidebar"
                            aria-expanded="false"
                        >Menu</button>
                    <?php endif; ?>
                    <div>
                        <p class="eyebrow">Gestão de restaurante</p>
                        <h1><?= e($appName) ?></h1>
                    </div>
                </div>
                <div class="topbar-actions">
                    <button type="button" class="theme-toggle" data-theme-toggle>Modo noturno</button>
                    <?php if (is_array($user)): ?>
                        <div class="user-chip">
                            <strong><?= e($user['name']) ?></strong>
                            <span><?= e(ucfirst($user['role'])) ?></span>
                        </div>
                        <form method="post" action="/logout">
                            <?= csrf_field() ?>
                            <button type="submit" class="button button-ghost">Sair</button>
                        </form>
                    <?php endif; ?>
                </div>
            </header>

            <?php if ($flashMessages !== []): ?>
                <section class="flash-stack">
                    <?php foreach ($flashMessages as $type => $message): ?>
                        <div class="flash flash-<?= e($type) ?>"><?= e($message) ?></div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</body>
</html>
