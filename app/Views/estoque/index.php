<?php
$title = 'Estoque';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 module-title"><i class="bi bi-box-seam me-2"></i>Controle de Estoque</h1>
    <a href="/estoque/saida-materiais" class="btn btn-outline-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>Saída de Materiais</a>
</div>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/dashboard"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<input type="text" class="form-control mb-3" placeholder="Filtrar ao digitar..." data-filter-input="#tabela-estoque">
<div class="table-responsive">
    <table class="table table-striped" id="tabela-estoque" data-filter-table>
        <thead><tr><th>Código</th><th>Produto</th><th>Unid.</th><th>Saldo</th><th>Mínimo</th></tr></thead>
        <tbody>
        <?php foreach ($registros as $r): ?>
            <tr>
                <td><?= htmlspecialchars((string) $r['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $r['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $r['unidade'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $r['saldo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $r['minimo'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
