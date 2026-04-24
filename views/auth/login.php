<section class="auth-shell">
    <div class="auth-hero">
        <p class="eyebrow">Gestão completa para restaurante</p>
        <h1>Experiência premium para operação, caixa e atendimento em tempo real.</h1>
        <p class="lead text-secondary-light">Este ambiente inicial simula autenticação, menu administrativo e componentes principais do layout definido em sistema.md.</p>

        <div class="hero-metrics">
            <div class="metric-card">
                <span>27</span>
                <small>pedidos ativos</small>
            </div>
            <div class="metric-card">
                <span>94</span>
                <small>NPS do salão</small>
            </div>
            <div class="metric-card">
                <span>14 min</span>
                <small>tempo médio</small>
            </div>
        </div>
    </div>

    <div class="card auth-card panel-card">
        <div class="card-body p-4 p-lg-5">
            <p class="eyebrow">Acesso ao sistema</p>
            <h2 class="section-title mb-3">Login demonstrativo</h2>
            <p class="text-secondary-light mb-4">Use qualquer usuário abaixo com a senha <strong>admin123</strong>.</p>

            <form action="<?= e(url('login')) ?>" method="post" class="auth-form">
                <input type="hidden" name="_token" value="<?= e(App\Core\Session::csrfToken()) ?>">

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="admin@techfood.local" value="<?= e($oldEmail ?? '') ?>" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Digite a senha" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-4">Entrar no painel</button>
            </form>

            <div id="credenciais-demo" class="demo-credentials">
                <div class="demo-header">
                    <h3 class="h6 mb-0">Usuários simulados</h3>
                    <span class="badge text-bg-dark">Banco ainda não implementado</span>
                </div>

                <?php foreach ($demoUsers as $user): ?>
                    <button
                        type="button"
                        class="demo-user"
                        data-demo-email="<?= e($user['email']) ?>"
                        data-demo-password="admin123"
                    >
                        <span class="avatar-pill"><?= e($user['avatar']) ?></span>
                        <span>
                            <strong><?= e($user['name']) ?></strong>
                            <small><?= e($user['role']) ?> • <?= e($user['email']) ?></small>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>