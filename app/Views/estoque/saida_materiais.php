<?php
$title = 'Saída de Materiais';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-box-arrow-up-right me-2"></i>Saída de Materiais</h1>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/estoque"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<input type="text" class="form-control mb-3" placeholder="Filtrar ao digitar..." data-filter-input="#tabela-saida">
<table class="table table-striped" id="tabela-saida" data-filter-table>
    <thead><tr><th>Data</th><th>Motivo</th><th>Responsável</th><th>Quantidade</th></tr></thead>
    <tbody>
    <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= htmlspecialchars((string) $r['data'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['motivo'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['responsavel'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['qtd'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
