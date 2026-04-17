<section class="guest-shell client-shell">
    <div class="guest-panel">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Preview do cardápio</p>
                <h1>Experiência do cliente</h1>
            </div>
            <a href="/admin" class="button button-ghost">Voltar ao painel</a>
        </div>

        <?php foreach ($menuByCategory as $category => $items): ?>
            <section class="menu-category-section">
                <div class="section-heading compact-heading">
                    <div>
                        <p class="eyebrow">Categoria</p>
                        <h2><?= e($category) ?></h2>
                    </div>
                </div>
                <div class="menu-card-grid">
                    <?php foreach ($items as $item): ?>
                        <article class="menu-card-item">
                            <div class="menu-image" style="--img:url('<?= e($item['image_path'] ?: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80') ?>'); --zoom:<?= e((string) $item['image_zoom']) ?>%; --pos-x:<?= e((string) $item['image_position_x']) ?>%; --pos-y:<?= e((string) $item['image_position_y']) ?>%;"></div>
                            <div class="menu-card-content">
                                <strong><?= e($item['title']) ?></strong>
                                <p><?= e($item['description']) ?></p>
                                <div class="price-row">
                                    <span><?= e(money((float) $item['sale_price'])) ?></span>
                                    <span class="badge badge-neutral"><?= e($item['service_group']) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</section>
