<?php $user = auth_user(); ?>
<aside class="sidebar">
    <div class="brand-block">
        <span class="brand-mark">TF</span>
        <div>
            <p class="eyebrow mb-1">SaaS Multi-Tenant</p>
            <h1 class="brand-name mb-0">Tech-Food</h1>
        </div>
    </div>

    <div class="tenant-card">
        <span class="status-dot"></span>
        <div>
            <strong><?= e($user['tenant'] ?? 'Tenant demo') ?></strong>
            <p class="mb-0 text-secondary-light">Operação ao vivo do salão</p>
        </div>
    </div>

    <nav class="nav flex-column sidebar-nav">
        <a class="nav-link <?= is_active_path('/admin') ? 'active' : '' ?>" href="<?= e(url('admin')) ?>">
            <i class="bi bi-grid"></i>
            <span>Visão geral</span>
        </a>
        <a class="nav-link" href="#operacao">
            <i class="bi bi-cup-hot"></i>
            <span>Cozinha e copa</span>
        </a>
        <a class="nav-link" href="#mesas">
            <i class="bi bi-qr-code-scan"></i>
            <span>Mesas e QR-Code</span>
        </a>
        <a class="nav-link" href="#financeiro">
            <i class="bi bi-credit-card-2-front"></i>
            <span>Caixa e pagamentos</span>
        </a>
        <a class="nav-link" href="#cadastros">
            <i class="bi bi-people"></i>
            <span>Equipe e acessos</span>
        </a>
    </nav>

    <div class="sidebar-footer card panel-card">
        <div class="card-body">
            <p class="eyebrow mb-2">Status do ambiente</p>
            <h2 class="section-title h5 mb-2">Modo demonstração</h2>
            <p class="text-secondary-light mb-3">Usuários simulados em memória para validar fluxo, layout e navegação antes do banco real.</p>
            <a class="btn btn-outline-primary w-100" href="#credenciais-demo">Ver credenciais</a>
        </div>
    </div>
</aside>