<?php $flashMessages = flash(); ?>

<section class="guest-shell client-shell">
    <div class="guest-panel client-panel">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Mesa digital</p>
                <h1>Mesa <?= e((string) $table['number']) ?></h1>
                <p class="muted">Use o cardápio guiado para montar o pedido completo e confirmar tudo antes do envio.</p>
            </div>
            <?php if ($session !== null): ?>
                <span class="badge badge-success">Atendimento em nome de <?= e($session['customer_name']) ?></span>
            <?php else: ?>
                <span class="badge badge-neutral">Aguardando identificação do cliente</span>
            <?php endif; ?>
        </div>

        <?php if ($flashMessages !== []): ?>
            <section class="flash-stack">
                <?php foreach ($flashMessages as $type => $message): ?>
                    <div class="flash flash-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if ($session === null): ?>
            <div class="panel-grid two-columns">
                <article class="info-card">
                    <p class="eyebrow">Início do atendimento</p>
                    <h2>Abra a mesa para acessar o cardápio</h2>
                    <p class="muted">Primeiro registramos o nome do cliente. Depois disso, o cardápio completo é liberado com filtro por categoria, configuração item a item e revisão final.</p>

                    <form method="post" action="/mesa/<?= e($tableToken) ?>/register" class="form-grid" data-register-form>
                        <?= csrf_field() ?>
                        <input type="hidden" name="customer_name" value="">
                        <button type="button" class="button button-primary full-width" data-register-table>Informar nome e abrir cardápio</button>
                    </form>
                </article>

                <aside class="info-card">
                    <p class="eyebrow">Como funciona</p>
                    <div class="stack-list compact-stack">
                        <div class="stack-item">
                            <strong>1. Identificação</strong>
                            <p>O nome do cliente é registrado para abrir a comanda.</p>
                        </div>
                        <div class="stack-item">
                            <strong>2. Seleção guiada</strong>
                            <p>Escolha a categoria, configure quantidade, remoções, adicionais e observações.</p>
                        </div>
                        <div class="stack-item">
                            <strong>3. Conferência final</strong>
                            <p>Revise todos os itens antes de enviar o pedido para a equipe.</p>
                        </div>
                    </div>
                </aside>
            </div>
        <?php else: ?>
            <div class="panel-grid client-layout">
                <form method="post" action="/mesa/<?= e($tableToken) ?>/order" class="info-card client-guided-order" data-client-order-flow>
                    <?= csrf_field() ?>
                    <input type="hidden" name="cart_payload" value="[]" data-client-cart-payload>

                    <div class="section-heading">
                        <div>
                            <p class="eyebrow">Cardápio digital</p>
                            <h2>Monte seu pedido</h2>
                            <p class="muted">Escolha a categoria, configure cada item e revise tudo antes de confirmar.</p>
                        </div>
                        <div class="client-order-chips">
                            <span class="badge badge-neutral">Mesa <?= e((string) $table['number']) ?></span>
                            <span class="badge badge-success"><?= e($session['customer_name']) ?></span>
                        </div>
                    </div>

                    <div class="client-filter-bar">
                        <label class="field">
                            <span>Categoria</span>
                            <select data-client-category-filter>
                                <option value="">Todas</option>
                                <?php foreach (array_keys($menuByCategory) as $category): ?>
                                    <option value="<?= e($category) ?>"><?= e($category) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="field">
                            <span>Buscar item</span>
                            <input type="search" placeholder="Digite o nome do item" data-client-search-filter>
                        </label>
                    </div>

                    <div class="catalog-scroll client-catalog-scroll">
                        <?php foreach ($menuByCategory as $category => $items): ?>
                            <section class="menu-category-section" data-client-category-section data-category-name="<?= e($category) ?>">
                                <div class="section-heading compact-heading">
                                    <div>
                                        <p class="eyebrow">Categoria</p>
                                        <h2><?= e($category) ?></h2>
                                    </div>
                                </div>
                                <div class="menu-card-grid">
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
                                        <article class="menu-card-item" data-client-menu-card data-category-name="<?= e($category) ?>" data-search-text="<?= e(strtolower($item['title'] . ' ' . $item['description'])) ?>">
                                            <div class="menu-image" style="--img:url('<?= e($item['image_path'] ?: 'https://images.unsplash.com/photo-1550317138-10000687a72b?auto=format&fit=crop&w=900&q=80') ?>'); --zoom:<?= e((string) $item['image_zoom']) ?>%; --pos-x:<?= e((string) $item['image_position_x']) ?>%; --pos-y:<?= e((string) $item['image_position_y']) ?>%;"></div>
                                            <div class="menu-card-content">
                                                <strong><?= e($item['title']) ?></strong>
                                                <p><?= e($item['description']) ?></p>
                                                <div class="price-row">
                                                    <span><?= e(money((float) $item['sale_price'])) ?></span>
                                                    <button type="button" class="button button-primary small" data-client-menu-item='<?= e(json_encode($itemPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>Selecionar</button>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>

                    <div class="client-cart-footer">
                        <div class="client-cart-meter">
                            <strong data-client-cart-count>0 itens</strong>
                            <span class="muted">Total atual: <strong data-client-cart-total>R$ 0,00</strong></span>
                        </div>
                        <button type="button" class="button button-primary" data-client-open-review disabled>Finalizar pedido</button>
                    </div>

                    <div class="client-flow-overlay is-hidden" data-client-item-overlay></div>
                    <aside class="client-item-panel is-hidden" data-client-item-panel>
                        <div class="section-heading compact-heading">
                            <div>
                                <p class="eyebrow">Configuração do item</p>
                                <h3 data-client-panel-title>Item selecionado</h3>
                            </div>
                            <button type="button" class="button button-ghost small" data-client-panel-close>Fechar</button>
                        </div>

                        <div class="stack-list client-panel-content">
                            <p class="muted" data-client-panel-description></p>

                            <label class="field">
                                <span>Quantidade</span>
                                <input type="number" min="1" value="1" data-client-panel-quantity>
                            </label>

                            <div class="field">
                                <span>Retirar ingredientes</span>
                                <div class="stack-list compact-stack" data-client-panel-removables>
                                    <p class="muted">Nenhuma remoção disponível para este item.</p>
                                </div>
                            </div>

                            <div class="field">
                                <span>Adicionais</span>
                                <div class="stack-list compact-stack" data-client-panel-additionals>
                                    <p class="muted">Nenhum adicional disponível para este item.</p>
                                </div>
                            </div>

                            <label class="field is-hidden" data-client-panel-delivery-wrapper>
                                <span>Entrega</span>
                                <select data-client-panel-delivery>
                                    <option value="immediate">Entregar agora</option>
                                    <option value="with_order">Junto com o restante</option>
                                </select>
                            </label>

                            <label class="field">
                                <span>Observações</span>
                                <textarea rows="4" placeholder="Ex.: sem gelo, carne ao ponto" data-client-panel-notes></textarea>
                            </label>

                            <div class="stack-item total-row">
                                <span>Total do item</span>
                                <strong data-client-panel-total>R$ 0,00</strong>
                            </div>

                            <div class="toolbar-actions">
                                <button type="button" class="button button-ghost" data-client-panel-close>Cancelar</button>
                                <button type="button" class="button button-primary" data-client-panel-save>Incluir no pedido</button>
                            </div>
                        </div>
                    </aside>

                    <div class="client-flow-overlay is-hidden" data-client-review-overlay></div>
                    <aside class="client-review-panel is-hidden" data-client-review-panel>
                        <div class="section-heading compact-heading">
                            <div>
                                <p class="eyebrow">Conferência</p>
                                <h3>Resumo do pedido</h3>
                            </div>
                            <button type="button" class="button button-ghost small" data-client-close-review>Fechar</button>
                        </div>

                        <div class="stack-list" data-client-review-list>
                            <p class="muted">Nenhum item adicionado ainda.</p>
                        </div>

                        <div class="stack-item total-row">
                            <span>Total final</span>
                            <strong data-client-review-total>R$ 0,00</strong>
                        </div>

                        <div class="toolbar-actions client-review-actions">
                            <button type="button" class="button button-ghost" data-client-close-review>Continuar escolhendo</button>
                            <button type="submit" class="button button-primary" data-client-submit-cart disabled>Confirmar pedido</button>
                        </div>
                    </aside>
                </form>

                <aside class="client-summary-column">
                    <div class="info-card sticky-card">
                        <p class="eyebrow">Mesa em andamento</p>
                        <h2>Conta atual</h2>

                        <div class="stack-item">
                            <span>Subtotal da mesa</span>
                            <strong><?= e(money((float) $subtotal)) ?></strong>
                        </div>

                        <div class="stack-list compact-stack">
                            <?php foreach ($sessionOrders as $order): ?>
                                <div class="stack-item">
                                    <div>
                                        <strong><?= e($order['items']) ?></strong>
                                        <p><?= e($order['status']) ?> · <?= e(date('H:i', strtotime($order['created_at']))) ?></p>
                                    </div>
                                    <span><?= e(money((float) $order['total_amount'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <form method="post" action="/mesa/<?= e($tableToken) ?>/payment" class="form-grid">
                            <?= csrf_field() ?>
                            <label class="field">
                                <span>Forma de pagamento</span>
                                <select name="payment_method">
                                    <option value="pix">Pix</option>
                                    <option value="credit_card">Cartão crédito</option>
                                    <option value="debit_card">Cartão débito</option>
                                    <option value="cash">Dinheiro</option>
                                </select>
                            </label>
                            <button type="submit" class="button button-ghost full-width">Solicitar fechamento</button>
                        </form>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
