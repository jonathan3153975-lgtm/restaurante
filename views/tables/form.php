<section class="info-card form-panel narrow-panel">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Cadastro de mesas</p>
            <h2>Nova mesa</h2>
        </div>
        <a href="/admin/tables" class="button button-ghost">Voltar</a>
    </div>

    <form method="post" action="/admin/tables/store" class="form-grid">
        <?= csrf_field() ?>
        <label class="field">
            <span>Número da mesa</span>
            <input type="number" name="number" min="1" required>
        </label>
        <label class="field">
            <span>Quantidade de cadeiras</span>
            <input type="number" name="seats" min="1" required>
        </label>
        <label class="field inline-field">
            <input type="checkbox" name="is_active" checked>
            <span>Mesa ativa</span>
        </label>
        <button type="submit" class="button button-primary">Salvar mesa</button>
    </form>
</section>
