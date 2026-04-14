<?php
$title = 'RH';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-people me-2"></i>Funcionários e RH</h1>
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button"><i class="bi bi-plus-circle me-1"></i>Novo</button>
    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-pencil-square me-1"></i>Editar</button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar</a>
    <a class="btn btn-gold btn-sm" href="/dashboard"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label" for="cpf-demo">CPF</label>
        <input id="cpf-demo" class="form-control" data-mask="cpf" placeholder="000.000.000-00">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="telefone-demo">Telefone</label>
        <input id="telefone-demo" class="form-control" data-mask="telefone" placeholder="(00) 00000-0000">
    </div>
</div>
<input type="text" class="form-control mb-3" placeholder="Filtrar ao digitar..." data-filter-input="#tabela-rh">
<table class="table table-striped" id="tabela-rh" data-filter-table>
    <thead><tr><th>Nome</th><th>CPF</th><th>Telefone</th><th>Cargo</th></tr></thead>
    <tbody>
    <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= htmlspecialchars((string) $r['nome'], ENT_QUOTES, 'UTF-8') ?></td>
            <td data-mask-static="cpf"><?= htmlspecialchars((string) $r['cpf'], ENT_QUOTES, 'UTF-8') ?></td>
            <td data-mask-static="telefone"><?= htmlspecialchars((string) $r['telefone'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $r['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../layouts/main.php';
