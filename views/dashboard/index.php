<section class="panel-grid metrics-grid">
    <article class="info-card metric-card">
        <span>Itens cadastrados</span>
        <strong><?= e((string) $stats['menu_items']) ?></strong>
    </article>
    <article class="info-card metric-card">
        <span>Mesas ocupadas</span>
        <strong><?= e((string) $stats['active_tables']) ?></strong>
    </article>
    <article class="info-card metric-card">
        <span>Pedidos em aberto</span>
        <strong><?= e((string) $stats['open_orders']) ?></strong>
    </article>
    <article class="info-card metric-card">
        <span>Faturamento do dia</span>
        <strong><?= e(money((float) $stats['today_revenue'])) ?></strong>
    </article>
</section>

<section class="info-card simulator-card">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Operação assistida</p>
            <h2>Simulador de pedido completo</h2>
        </div>
        <button type="button" class="button button-primary" data-open-admin-order-modal data-redirect-to="/admin">Simular pedido</button>
    </div>
    <p class="muted">Monte um pedido completo pelo painel, selecionando mesa, cliente, itens, adicionais e observações, sem depender do QR Code.</p>
</section>

<?php
$modalId = 'admin-order-modal';
$modalTitle = 'Simular pedido administrativo';
$modalDescription = 'Use este fluxo para testar o cardápio ou registrar um pedido completo pela equipe.';
$submitLabel = 'Gerar pedido';
$redirectTo = '/admin';
require App\Core\App::basePath('views/partials/admin-order-modal.php');
?>

<section class="panel-grid two-columns">
    <article class="info-card">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Fluxo operacional</p>
                <h2>Atalhos principais</h2>
            </div>
        </div>

        <div class="shortcut-grid">
            <a href="/admin/menu" class="shortcut-card">
                <strong>Cardápio</strong>
                <span>Cadastro, estoque e preview.</span>
            </a>
            <a href="/admin/tables" class="shortcut-card">
                <strong>Mesas</strong>
                <span>QR-Code, ocupação e atendimento manual.</span>
            </a>
            <a href="/admin/orders" class="shortcut-card">
                <strong>Pedidos</strong>
                <span>Fila priorizada por tempo de espera.</span>
            </a>
            <a href="/admin/cashier" class="shortcut-card">
                <strong>Caixa</strong>
                <span>Fechamento rápido por método de pagamento.</span>
            </a>
        </div>
    </article>

    <article class="info-card">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Tempo real operacional</p>
                <h2>Últimos pedidos</h2>
            </div>
        </div>

        <div class="stack-list">
            <?php foreach ($recentOrders as $order): ?>
                <div class="stack-item">
                    <div>
                        <strong>Mesa <?= e((string) $order['table_number']) ?> · <?= e($order['customer_name']) ?></strong>
                        <p><?= e(date('d/m H:i', strtotime($order['created_at']))) ?> · <?= e($order['status']) ?></p>
                    </div>
                    <span><?= e(money((float) $order['total_amount'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
