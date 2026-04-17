<?php $user = current_user(); ?>
<aside class="sidebar" id="app-sidebar" data-app-sidebar>
    <div class="sidebar-header">
        <div class="brand-block">
            <span class="brand-badge">TF</span>
            <div>
                <strong>Tech-Food</strong>
                <p>Operação, pedidos e caixa</p>
            </div>
        </div>
        <button type="button" class="button button-ghost small sidebar-close" data-sidebar-close>Fechar</button>
    </div>

    <?php if ($user !== null): ?>
        <nav class="sidebar-nav">
            <a href="/admin" class="nav-link">Visão geral</a>
            <a href="/admin/menu" class="nav-link">Cardápio</a>
            <a href="/admin/tables" class="nav-link">Mesas</a>
            <a href="/admin/orders" class="nav-link">Pedidos</a>
            <a href="/admin/cashier" class="nav-link">Caixa</a>
            <a href="/admin/menu/preview" class="nav-link">Preview do cardápio</a>
        </nav>
    <?php else: ?>
        <nav class="sidebar-nav">
            <a href="/login" class="nav-link">Entrar</a>
        </nav>
    <?php endif; ?>
</aside>
