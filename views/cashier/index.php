<section class="info-card">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Financeiro operacional</p>
            <h2>Caixa e encerramentos</h2>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mesa</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Subtotal</th>
                    <th>Pagamento</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?= e((string) $session['table_number']) ?></td>
                        <td><?= e($session['customer_name']) ?></td>
                        <td><?= e($session['status']) ?></td>
                        <td><?= e(money((float) $session['subtotal'])) ?></td>
                        <td><?= e((string) ($session['payment_method'] ?: '-')) ?></td>
                        <td>
                            <?php if ($session['status'] === 'open'): ?>
                                <form method="post" action="/admin/cashier/<?= e((string) $session['id']) ?>/checkout" class="inline-actions">
                                    <?= csrf_field() ?>
                                    <select name="payment_method">
                                        <option value="pix">Pix</option>
                                        <option value="credit_card">Cartão crédito</option>
                                        <option value="debit_card">Cartão débito</option>
                                        <option value="cash">Dinheiro</option>
                                    </select>
                                    <button type="submit" class="button button-primary small">Fechar conta</button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-success">Encerrada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
