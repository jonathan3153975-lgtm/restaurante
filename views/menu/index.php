<section class="info-card">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Cadastro operacional</p>
            <h2>Itens do cardápio</h2>
        </div>
        <div class="toolbar-actions">
            <a href="/admin/menu/preview" class="button button-ghost">Ver cardápio</a>
            <a href="/admin/menu/create" class="button button-primary">Novo item</a>
        </div>
    </div>

    <form method="get" action="/admin/menu" class="toolbar-grid">
        <label class="field">
            <span>Busca dinâmica</span>
            <input type="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Título ou descrição" data-live-filter-input="menu-table">
        </label>
        <label class="field">
            <span>Categoria</span>
            <select name="category_id">
                <option value="">Todas</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e((string) $category['id']) ?>" <?= $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="toolbar-actions align-end">
            <button type="submit" class="button">Filtrar</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody data-live-filter-target="menu-table">
                <?php foreach ($items['data'] as $item): ?>
                    <tr data-live-filter-row>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <p><?= e($item['description']) ?></p>
                        </td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><?= e(money((float) $item['sale_price'])) ?></td>
                        <td><?= $item['is_stockable'] ? e((string) $item['stock_quantity']) : 'Nao controlado' ?></td>
                        <td>
                            <span class="badge <?= (int) $item['is_active'] === 1 ? 'badge-success' : 'badge-neutral' ?>">
                                <?= (int) $item['is_active'] === 1 ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="inline-actions">
                                <a href="/admin/menu/<?= e((string) $item['id']) ?>/edit" class="button button-ghost small">Editar</a>
                                <form method="post" action="/admin/menu/<?= e((string) $item['id']) ?>/delete" onsubmit="return confirm('Excluir este item?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="button button-danger small">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $paginator = $items;
    $basePath = '/admin/menu';
    $query = ['search' => $filters['search'], 'category_id' => $filters['category_id']];
    require App\Core\App::basePath('views/partials/pagination.php');
    ?>
</section>
