<section class="auth-shell">
    <div class="guest-panel auth-panel">
        <div>
            <p class="eyebrow">Acesso administrativo</p>
            <h1>Tech-Food</h1>
            <p class="muted">Painel para operação de salão, cardápio, pedidos e fechamento de conta.</p>
        </div>

        <form method="post" action="/login" class="form-grid">
            <?= csrf_field() ?>
            <label class="field">
                <span>E-mail</span>
                <input type="email" name="email" value="<?= e((string) old('email')) ?>" placeholder="admin@techfood.local" required>
            </label>

            <label class="field">
                <span>Senha</span>
                <input type="password" name="password" placeholder="Sua senha" required>
            </label>

            <button type="submit" class="button button-primary full-width">Entrar no painel</button>
        </form>

        <div class="info-card slim-card">
            <strong>Credenciais seed</strong>
            <p>Usuário inicial: admin@techfood.local</p>
            <p>Senha inicial: admin123</p>
        </div>
    </div>
</section>
