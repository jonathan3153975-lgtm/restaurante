<section class="info-card">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Expedição</p>
            <h2>Fila de pedidos</h2>
        </div>
    </div>

    <form method="get" action="/admin/orders" class="filter-pills">
        <button class="pill <?= $selectedType === '' ? 'is-active' : '' ?>" name="type" value="">Todos</button>
        <button class="pill <?= $selectedType === 'drink' ? 'is-active' : '' ?>" name="type" value="drink">Bebidas</button>
        <button class="pill <?= $selectedType === 'meal' ? 'is-active' : '' ?>" name="type" value="meal">Refeições</button>
        <button class="pill <?= $selectedType === 'dessert' ? 'is-active' : '' ?>" name="type" value="dessert">Sobremesas</button>
    </form>

    <div class="stack-list">
        <?php foreach ($orders as $order): ?>
            <article class="stack-item order-card">
                <div>
                    <strong>Mesa <?= e((string) $order['table_number']) ?> · <?= e($order['customer_name']) ?></strong>
                    <p><?= e($order['items']) ?></p>
                    <small><?= e(date('d/m H:i', strtotime($order['created_at']))) ?> · <?= e($order['delivery_timing']) ?></small>
                </div>
                <div class="toolbar-actions">
                    <span class="badge badge-warning"><?= e(money((float) $order['total_amount'])) ?></span>
                    <form method="post" action="/admin/orders/<?= e((string) $order['id']) ?>/delivered">
                        <?= csrf_field() ?>
                        <button type="submit" class="button button-primary small">Marcar entregue</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
