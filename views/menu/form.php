<?php
$item = $item ?? null;
$selectedCategory = (string) old('category_id', $item['category_id'] ?? '');
$titleValue = (string) old('title', $item['title'] ?? '');
$descriptionValue = (string) old('description', $item['description'] ?? '');
$costPriceValue = (string) old('cost_price', $item !== null ? number_format((float) $item['cost_price'], 2, ',', '.') : '');
$salePriceValue = (string) old('sale_price', $item !== null ? number_format((float) $item['sale_price'], 2, ',', '.') : '');
$imagePath = (string) ($item['image_path'] ?? '');
$imageZoom = (string) old('image_zoom', $item['image_zoom'] ?? '115');
$imagePositionX = (string) old('image_position_x', $item['image_position_x'] ?? '50');
$imagePositionY = (string) old('image_position_y', $item['image_position_y'] ?? '50');
$isStockable = (int) old('is_stockable', $item['is_stockable'] ?? 0) === 1;
$stockQuantity = (string) old('stock_quantity', $item['stock_quantity'] ?? '0');
$isActive = (int) old('is_active', $item['is_active'] ?? 1) === 1;

$oldRemovable = old('removable_ingredients', null);
$removableIngredients = is_array($oldRemovable)
    ? array_values(array_filter(array_map(static fn (mixed $ingredient): string => trim((string) $ingredient), $oldRemovable)))
    : ($item !== null ? (json_decode((string) $item['removable_ingredients'], true) ?: []) : []);

$oldAdditionalNames = old('additional_names', null);
$oldAdditionalPrices = old('additional_prices', null);
$additionalRows = [];

if (is_array($oldAdditionalNames)) {
    foreach ($oldAdditionalNames as $index => $name) {
        $trimmedName = trim((string) $name);
        $price = (string) ($oldAdditionalPrices[$index] ?? '');

        if ($trimmedName === '' && trim($price) === '') {
            continue;
        }

        $additionalRows[] = [
            'name' => $trimmedName,
            'price' => $price,
        ];
    }
} elseif ($item !== null) {
    foreach (json_decode((string) $item['additionals'], true) ?: [] as $additional) {
        $additionalRows[] = [
            'name' => (string) ($additional['name'] ?? ''),
            'price' => number_format((float) ($additional['price'] ?? 0), 2, ',', '.'),
        ];
    }
}

if ($removableIngredients === []) {
    $removableIngredients = [''];
}

if ($additionalRows === []) {
    $additionalRows = [['name' => '', 'price' => '']];
}

$previewImage = $imagePath !== '' ? $imagePath : 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=900&q=80';
?>
<section class="info-card form-panel">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Cardápio</p>
            <h2><?= e($title) ?></h2>
        </div>
        <a href="/admin/menu" class="button button-ghost">Voltar</a>
    </div>

    <form method="post" action="<?= e($formAction) ?>" enctype="multipart/form-data" class="form-grid two-columns">
        <?= csrf_field() ?>

        <div class="field-group span-2">
            <label class="field">
                <span>Categoria</span>
                <select name="category_id" data-category-select required>
                    <option value="">Selecione</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e((string) $category['id']) ?>" <?= $selectedCategory === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="button" class="button button-ghost align-self-end" data-open-category-modal>Nova categoria</button>
        </div>

        <label class="field">
            <span>Título</span>
            <input type="text" name="title" value="<?= e($titleValue) ?>" required>
        </label>

        <div class="field span-2" data-image-cropper>
            <div class="section-heading compact-heading cropper-heading">
                <div>
                    <span>Imagem</span>
                    <p class="muted">Faça upload, reposicione e confira o enquadramento antes de salvar.</p>
                </div>
            </div>

            <div class="image-cropper-layout">
                <label class="field image-upload-field">
                    <span>Arquivo</span>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-image-input>
                </label>

                <div class="image-cropper-stage">
                    <img src="<?= e($previewImage) ?>" alt="Preview da imagem do item" data-image-preview-image>
                </div>

                <div class="image-cropper-side">
                    <div class="menu-card-item preview-card">
                        <div class="menu-image preview-menu-image" data-image-preview-card style="--img:url('<?= e($previewImage) ?>'); --zoom:<?= e($imageZoom) ?>%; --pos-x:<?= e($imagePositionX) ?>%; --pos-y:<?= e($imagePositionY) ?>%;"></div>
                        <div class="menu-card-content">
                            <strong>Preview do cardápio</strong>
                            <p>Este recorte será usado no card do item.</p>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="image_zoom" value="<?= e($imageZoom) ?>" data-image-zoom>
            <input type="hidden" name="image_position_x" value="<?= e($imagePositionX) ?>" data-image-position-x>
            <input type="hidden" name="image_position_y" value="<?= e($imagePositionY) ?>" data-image-position-y>
        </div>

        <label class="field span-2">
            <span>Descrição</span>
            <textarea name="description" rows="4" required><?= e($descriptionValue) ?></textarea>
        </label>

        <div class="field" data-repeatable-root>
            <span>Ingredientes removíveis</span>
            <div class="repeatable-list" data-repeatable-list>
                <?php foreach ($removableIngredients as $ingredient): ?>
                    <div class="repeatable-row">
                        <input type="text" name="removable_ingredients[]" value="<?= e((string) $ingredient) ?>" placeholder="Ex.: cebola roxa">
                        <button type="button" class="button button-ghost small" data-repeatable-remove>Remover</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-ghost small" data-repeatable-add>Adicionar ingrediente removível</button>
            <template data-repeatable-template>
                <div class="repeatable-row">
                    <input type="text" name="removable_ingredients[]" value="" placeholder="Ex.: cebola roxa">
                    <button type="button" class="button button-ghost small" data-repeatable-remove>Remover</button>
                </div>
            </template>
        </div>

        <div class="field" data-repeatable-root>
            <span>Adicionais</span>
            <div class="repeatable-list" data-repeatable-list>
                <?php foreach ($additionalRows as $additional): ?>
                    <div class="repeatable-row additional-row">
                        <input type="text" name="additional_names[]" value="<?= e($additional['name']) ?>" placeholder="Nome do adicional">
                        <input type="text" name="additional_prices[]" value="<?= e($additional['price']) ?>" placeholder="0,00" class="money-mask">
                        <button type="button" class="button button-ghost small" data-repeatable-remove>Remover</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-ghost small" data-repeatable-add>Adicionar adicional</button>
            <template data-repeatable-template>
                <div class="repeatable-row additional-row">
                    <input type="text" name="additional_names[]" value="" placeholder="Nome do adicional">
                    <input type="text" name="additional_prices[]" value="" placeholder="0,00" class="money-mask">
                    <button type="button" class="button button-ghost small" data-repeatable-remove>Remover</button>
                </div>
            </template>
        </div>

        <label class="field">
            <span>Preço de custo</span>
            <input type="text" name="cost_price" class="money-mask" value="<?= e($costPriceValue) ?>">
        </label>

        <label class="field">
            <span>Preço de venda</span>
            <input type="text" name="sale_price" class="money-mask" value="<?= e($salePriceValue) ?>" required>
        </label>

        <label class="field inline-field span-2 stock-toggle-row">
            <input type="checkbox" name="is_stockable" <?= $isStockable ? 'checked' : '' ?> data-stock-toggle>
            <span>Controlar estoque</span>
        </label>

        <label class="field <?= $isStockable ? '' : 'is-disabled' ?>" data-stock-wrapper>
            <span>Quantidade em estoque</span>
            <input type="number" name="stock_quantity" min="0" value="<?= e($stockQuantity) ?>" <?= $isStockable ? '' : 'disabled' ?> data-stock-input>
        </label>

        <label class="field inline-field">
            <input type="checkbox" name="is_active" <?= $isActive ? 'checked' : '' ?>>
            <span>Item ativo no cardápio</span>
        </label>

        <div class="toolbar-actions span-2">
            <button type="submit" class="button button-primary">Salvar item</button>
        </div>
    </form>

    <dialog class="inline-modal" data-category-modal>
        <form method="dialog" class="modal-content" data-category-form>
            <h3>Nova categoria</h3>
            <label class="field">
                <span>Nome da categoria</span>
                <input type="text" name="name" required>
            </label>
            <label class="field">
                <span>Tipo operacional</span>
                <select name="service_group">
                    <option value="meal">Refeição</option>
                    <option value="drink">Bebida</option>
                    <option value="dessert">Sobremesa</option>
                    <option value="other">Outro</option>
                </select>
            </label>
            <div class="toolbar-actions">
                <button type="button" class="button button-ghost" data-close-category-modal>Cancelar</button>
                <button type="submit" class="button button-primary">Salvar categoria</button>
            </div>
        </form>
    </dialog>
</section>
