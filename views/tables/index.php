<section class="info-card">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Salão</p>
            <h2>Mesas e QR-Codes</h2>
        </div>
        <a href="/admin/tables/create" class="button button-primary">Nova mesa</a>
    </div>

    <form method="get" action="/admin/tables" class="toolbar-grid">
        <label class="field">
            <span>Busca dinâmica</span>
            <input type="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Número da mesa" data-live-filter-input="tables-grid">
        </label>
        <label class="field">
            <span>Status</span>
            <select name="status">
                <option value="">Todas</option>
                <option value="free" <?= $filters['status'] === 'free' ? 'selected' : '' ?>>Livres</option>
                <option value="occupied" <?= $filters['status'] === 'occupied' ? 'selected' : '' ?>>Ocupadas</option>
            </select>
        </label>
        <div class="toolbar-actions align-end">
            <button type="submit" class="button">Filtrar</button>
        </div>
    </form>

    <div class="table-cards" data-live-filter-target="tables-grid">
        <?php foreach ($tables['data'] as $table): ?>
            <article class="info-card table-card" data-live-filter-row>
                <div class="section-heading compact-heading">
                    <div>
                        <h3>Mesa <?= e((string) $table['number']) ?></h3>
                        <p><?= e((string) $table['seats']) ?> cadeiras</p>
                    </div>
                    <span class="badge <?= $table['active_session_id'] !== null ? 'badge-warning' : 'badge-success' ?>">
                        <?= $table['active_session_id'] !== null ? 'Ocupada' : 'Livre' ?>
                    </span>
                </div>

                <?php if ($table['active_session_id'] !== null): ?>
                    <div class="stack-list compact-stack">
                        <div class="stack-item">
                            <span>Cliente</span>
                            <strong><?= e($table['customer_name']) ?></strong>
                        </div>
                        <div class="stack-item">
                            <span>Status do pedido</span>
                            <strong><?= e((string) ($table['order_status'] ?? 'Aguardando')) ?></strong>
                        </div>
                        <div class="stack-item">
                            <span>Tempo em aberto</span>
                            <strong><?= e((string) ($table['open_minutes'] ?? 0)) ?> min</strong>
                        </div>
                    </div>

                    <form method="post" action="/admin/tables/<?= e((string) $table['id']) ?>/close" class="form-grid">
                        <?= csrf_field() ?>
                        <label class="field">
                            <span>Encerrar com</span>
                            <select name="payment_method">
                                <option value="pix">Pix</option>
                                <option value="credit_card">Cartão crédito</option>
                                <option value="debit_card">Cartão débito</option>
                                <option value="cash">Dinheiro</option>
                            </select>
                        </label>
                        <button type="submit" class="button button-primary">Encerrar rapidamente</button>
                    </form>

                    <button
                        type="button"
                        class="button button-ghost full-width"
                        data-open-admin-order-modal
                        data-table-id="<?= e((string) $table['id']) ?>"
                        data-customer-name="<?= e((string) $table['customer_name']) ?>"
                        data-redirect-to="/admin/tables"
                    >Novo pedido da mesa</button>
                <?php else: ?>
                    <button
                        type="button"
                        class="button button-primary full-width"
                        data-open-admin-order-modal
                        data-table-id="<?= e((string) $table['id']) ?>"
                        data-redirect-to="/admin/tables"
                    >Ocupar mesa e lançar pedido</button>
                <?php endif; ?>

                <details class="qr-details">
                    <summary>Mostrar QR-Code da mesa</summary>
                    <img src="<?= e($qrCodes[(int) $table['id']]) ?>" alt="QR-Code da mesa <?= e((string) $table['number']) ?>" class="qr-image">
                    <a href="/mesa/<?= e($table['qr_token']) ?>" class="button button-ghost full-width" target="_blank" rel="noreferrer">Abrir cardápio do cliente</a>
                </details>

                <div class="inline-actions">
                    <form method="post" action="/admin/tables/<?= e((string) $table['id']) ?>/toggle">
                        <?= csrf_field() ?>
                        <button type="submit" class="button button-ghost small">
                            <?= (int) $table['is_active'] === 1 ? 'Desabilitar' : 'Habilitar' ?>
                        </button>
                    </form>
                    <form method="post" action="/admin/tables/<?= e((string) $table['id']) ?>/delete" onsubmit="return confirm('Excluir esta mesa?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="button button-danger small">Excluir</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php
    $paginator = $tables;
    $basePath = '/admin/tables';
    $query = ['search' => $filters['search'], 'status' => $filters['status']];
    require App\Core\App::basePath('views/partials/pagination.php');
    ?>
</section>

<?php
$modalId = 'admin-order-modal';
$modalTitle = 'Pedido completo da mesa';
$modalDescription = 'Abra a mesa, selecione os itens do cliente e envie o pedido em um único fluxo.';
$submitLabel = 'Abrir mesa e gerar pedido';
$redirectTo = '/admin/tables';
require App\Core\App::basePath('views/partials/admin-order-modal.php');
?>
