<?php
$modalId = $modalId ?? 'admin-order-modal';
$modalTitle = $modalTitle ?? 'Pedido administrativo';
$modalDescription = $modalDescription ?? 'Selecione a mesa e monte um pedido completo.';
$submitLabel = $submitLabel ?? 'Gerar pedido';
$redirectTo = $redirectTo ?? '/admin/orders';
$tableOptions = $tableOptions ?? [];
$menuByCategory = $menuByCategory ?? [];
?>
<dialog class="inline-modal" id="<?= e($modalId) ?>" data-admin-order-modal>
    <div class="modal-content wide-modal admin-order-modal">
        <div class="section-heading compact-heading">
            <div>
                <h3 data-order-modal-heading><?= e($modalTitle) ?></h3>
                <p class="muted"><?= e($modalDescription) ?></p>
            </div>
            <button type="button" class="button button-ghost small" data-close-admin-order-modal>Fechar</button>
        </div>

        <div class="admin-order-shell" data-admin-order-flow>
            <form method="post" action="/admin/orders/create" class="admin-order-form">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= e($redirectTo) ?>" data-order-redirect>
                <input type="hidden" name="table_id" value="" data-order-table-id>
                <input type="hidden" name="customer_name" value="" data-order-customer-name>
                <input type="hidden" name="cart_payload" value="[]" data-cart-payload>

                <section class="admin-step" data-admin-step="setup">
                    <div class="admin-setup-card">
                        <div>
                            <p class="eyebrow">Atendimento</p>
                            <h4>Selecione a mesa para iniciar</h4>
                            <p class="muted">Para mesas livres, o nome do cliente será solicitado antes da abertura do cardápio.</p>
                        </div>

                        <label class="field">
                            <span>Mesa</span>
                            <select data-order-table-select>
                                <option value="">Selecione a mesa</option>
                                <?php foreach ($tableOptions as $tableOption): ?>
                                    <option
                                        value="<?= e((string) $tableOption['id']) ?>"
                                        data-has-session="<?= $tableOption['active_session_id'] !== null ? '1' : '0' ?>"
                                        data-customer-name="<?= e((string) ($tableOption['customer_name'] ?? '')) ?>"
                                    >
                                        Mesa <?= e((string) $tableOption['number']) ?> · <?= $tableOption['active_session_id'] !== null ? 'ocupada' : 'livre' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <div class="stack-item compact-stack admin-setup-status">
                            <span>Atendimento</span>
                            <strong data-admin-setup-status>Selecione uma mesa para continuar.</strong>
                        </div>

                        <div class="toolbar-actions">
                            <button type="button" class="button button-primary" data-admin-start-order>Ir para o cardápio</button>
                        </div>
                    </div>
                </section>

                <section class="admin-step is-hidden" data-admin-step="catalog">
                    <div class="admin-order-header">
                        <div class="admin-order-chips">
                            <span class="badge badge-neutral" data-admin-table-chip>Mesa não definida</span>
                            <span class="badge badge-success" data-admin-customer-chip>Cliente não definido</span>
                        </div>
                        <div class="toolbar-actions">
                            <button type="button" class="button button-ghost small" data-admin-back-setup>Trocar mesa</button>
                            <button type="button" class="button button-primary small" data-admin-open-review disabled>Finalizar pedido</button>
                        </div>
                    </div>

                    <div class="admin-filter-bar">
                        <label class="field">
                            <span>Filtrar por categoria</span>
                            <select data-admin-category-filter>
                                <option value="">Todas</option>
                                <?php foreach (array_keys($menuByCategory) as $category): ?>
                                    <option value="<?= e($category) ?>"><?= e($category) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="field">
                            <span>Buscar item</span>
                            <input type="search" placeholder="Digite o nome do item" data-admin-search-filter>
                        </label>
                    </div>

                    <div class="catalog-scroll admin-catalog-scroll">
                        <?php if ($menuByCategory === []): ?>
                            <div class="empty-state">
                                <strong>Nenhum item disponível.</strong>
                                <p class="muted">Cadastre itens ativos no cardápio para usar este fluxo.</p>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($menuByCategory as $category => $items): ?>
                            <section class="menu-category-section" data-admin-category-section data-category-name="<?= e($category) ?>">
                                <div class="section-heading compact-heading">
                                    <div>
                                        <p class="eyebrow">Categoria</p>
                                        <h3><?= e($category) ?></h3>
                                    </div>
                                </div>
                                <div class="menu-card-grid compact-grid">
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                        $itemPayload = [
                                            'id' => (int) $item['id'],
                                            'title' => $item['title'],
                                            'description' => $item['description'],
                                            'sale_price' => (float) $item['sale_price'],
                                            'service_group' => $item['service_group'],
                                            'removable_ingredients' => json_decode((string) $item['removable_ingredients'], true) ?: [],
                                            'additionals' => json_decode((string) $item['additionals'], true) ?: [],
                                            'image_path' => $item['image_path'],
                                            'image_zoom' => (int) $item['image_zoom'],
                                            'image_position_x' => (int) $item['image_position_x'],
                                            'image_position_y' => (int) $item['image_position_y'],
                                            'category' => $category,
                                        ];
                                        ?>
                                        <article class="menu-card-item compact-card" data-admin-menu-card data-category-name="<?= e($category) ?>" data-search-text="<?= e(strtolower($item['title'] . ' ' . $item['description'])) ?>">
                                            <div class="menu-image" style="--img:url('<?= e($item['image_path'] ?: 'https://images.unsplash.com/photo-1550317138-10000687a72b?auto=format&fit=crop&w=900&q=80') ?>'); --zoom:<?= e((string) $item['image_zoom']) ?>%; --pos-x:<?= e((string) $item['image_position_x']) ?>%; --pos-y:<?= e((string) $item['image_position_y']) ?>%;"></div>
                                            <div class="menu-card-content">
                                                <strong><?= e($item['title']) ?></strong>
                                                <p><?= e($item['description']) ?></p>
                                                <div class="price-row">
                                                    <span><?= e(money((float) $item['sale_price'])) ?></span>
                                                    <button type="button" class="button button-primary small" data-admin-menu-item='<?= e(json_encode($itemPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>Selecionar</button>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>

                    <div class="admin-catalog-footer">
                        <div class="admin-cart-meter">
                            <strong data-admin-cart-count>0 itens</strong>
                            <span class="muted">Total atual: <strong data-cart-total>R$ 0,00</strong></span>
                        </div>
                        <button type="button" class="button button-primary" data-admin-open-review disabled>Revisar pedido</button>
                    </div>
                </section>

                <section class="admin-step is-hidden" data-admin-step="review">
                    <div class="section-heading compact-heading">
                        <div>
                            <p class="eyebrow">Conferência</p>
                            <h4>Resumo do pedido</h4>
                        </div>
                        <button type="button" class="button button-ghost small" data-admin-back-catalog>Voltar ao cardápio</button>
                    </div>

                    <div class="stack-list" data-admin-review-list>
                        <p class="muted">Nenhum item adicionado ainda.</p>
                    </div>

                    <div class="stack-item total-row">
                        <span>Total final</span>
                        <strong data-admin-review-total>R$ 0,00</strong>
                    </div>

                    <div class="toolbar-actions admin-review-actions">
                        <button type="button" class="button button-ghost" data-admin-back-catalog>Continuar escolhendo</button>
                        <button type="submit" class="button button-primary" data-submit-cart disabled><?= e($submitLabel) ?></button>
                    </div>
                </section>
            </form>

            <div class="admin-item-overlay is-hidden" data-admin-item-overlay></div>
            <aside class="admin-item-panel is-hidden" data-admin-item-panel>
                <div class="section-heading compact-heading">
                    <div>
                        <p class="eyebrow">Configuração do item</p>
                        <h4 data-admin-panel-title>Item selecionado</h4>
                    </div>
                    <button type="button" class="button button-ghost small" data-admin-panel-close>Fechar</button>
                </div>

                <div class="stack-list admin-panel-content">
                    <p class="muted" data-admin-panel-description></p>

                    <label class="field">
                        <span>Quantidade</span>
                        <input type="number" min="1" value="1" data-admin-panel-quantity>
                    </label>

                    <div class="field">
                        <span>Retirar ingredientes</span>
                        <div class="stack-list compact-stack" data-admin-panel-removables>
                            <p class="muted">Nenhuma remoção disponível para este item.</p>
                        </div>
                    </div>

                    <div class="field">
                        <span>Adicionais</span>
                        <div class="stack-list compact-stack" data-admin-panel-additionals>
                            <p class="muted">Nenhum adicional disponível para este item.</p>
                        </div>
                    </div>

                    <label class="field is-hidden" data-admin-panel-delivery-wrapper>
                        <span>Entrega</span>
                        <select data-admin-panel-delivery>
                            <option value="immediate">Entregar agora</option>
                            <option value="with_order">Junto com o restante</option>
                        </select>
                    </label>

                    <label class="field">
                        <span>Observações</span>
                        <textarea rows="4" placeholder="Ex.: sem gelo, carne ao ponto" data-admin-panel-notes></textarea>
                    </label>

                    <div class="stack-item total-row">
                        <span>Total do item</span>
                        <strong data-admin-panel-total>R$ 0,00</strong>
                    </div>

                    <div class="toolbar-actions">
                        <button type="button" class="button button-ghost" data-admin-panel-close>Cancelar</button>
                        <button type="button" class="button button-primary" data-admin-panel-save>Incluir no pedido</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</dialog>