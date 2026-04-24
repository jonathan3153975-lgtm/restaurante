<?php $user = auth_user(); ?>
<header class="topbar">
    <div>
        <button type="button" class="btn btn-outline-primary d-xl-none sidebar-toggle" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>
        <p class="eyebrow mb-1">Operação unificada</p>
        <h2 class="section-title mb-0">Painel premium do restaurante</h2>
    </div>

    <div class="topbar-actions">
        <span class="clock-chip" id="serviceClock"></span>

        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar-pill"><?= e($user['avatar'] ?? 'TF') ?></span>
                <span>
                    <?= e($user['name'] ?? 'Operador') ?>
                    <small><?= e($user['role'] ?? 'Perfil demo') ?></small>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-secondary-light">Tenant: <?= e($user['tenant'] ?? 'Tenant demo') ?></span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="<?= e(url('logout')) ?>" method="post" class="px-3 pb-2">
                        <input type="hidden" name="_token" value="<?= e(App\Core\Session::csrfToken()) ?>">
                        <button type="submit" class="btn btn-primary w-100">Sair</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>