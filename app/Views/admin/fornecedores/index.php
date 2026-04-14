<?php
$title = 'Fornecedores';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-shop me-2"></i>Cadastro de Fornecedores</h1>

<!-- Action Toolbar -->
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button" onclick="abrirFormulario(null)">
        <i class="bi bi-plus-circle me-1"></i>Novo Fornecedor
    </button>
    <a class="btn btn-outline-primary btn-sm" href="/relatorios">
        <i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar
    </a>
    <a class="btn btn-gold btn-sm" href="/dashboard">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<!-- Filtros -->
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Filtrar por nome ou CNPJ..." data-filter-input="#tabela-fornecedores" value="<?= htmlspecialchars($filtroTexto) ?>">
    </div>
    <div class="col-md-2">
        <select class="form-select" id="filtroMes" name="mes">
            <option value="">— Todos os meses</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $filtroMes === $m ? 'selected' : '' ?>>
                    <?= str_pad((string)$m, 2, '0', STR_PAD_LEFT) ?> - <?= [
                        '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                    ][$m] ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="filtroAno" name="ano">
            <option value="">— Todos os anos</option>
            <?php 
            $anoAtual = (int) date('Y');
            for ($a = $anoAtual; $a >= $anoAtual - 5; $a--): 
            ?>
                <option value="<?= $a ?>" <?= $filtroAno === $a ? 'selected' : '' ?>>
                    <?= $a ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-gold btn-sm w-100" onclick="aplicarFiltros()">
            <i class="bi bi-funnel me-1"></i>Filtrar
        </button>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary btn-sm w-100" onclick="limparFiltros()">
            <i class="bi bi-arrow-clockwise me-1"></i>Limpar
        </button>
    </div>
</div>

<!-- Tabela de Fornecedores -->
<div class="table-responsive">
    <table class="table table-striped" id="tabela-fornecedores" data-filter-table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>CNPJ</th>
                <th>Contato</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Data de Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($fornecedores)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                        Nenhum fornecedor encontrado.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($fornecedores as $f): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars((string) $f['id']) ?></strong></td>
                        <td><?= htmlspecialchars((string) $f['nome']) ?></td>
                        <td><?= !empty($f['cnpj']) ? htmlspecialchars((string) $f['cnpj']) : '—' ?></td>
                        <td><?= !empty($f['contato']) ? htmlspecialchars((string) $f['contato']) : '—' ?></td>
                        <td><?= !empty($f['telefone']) ? htmlspecialchars((string) $f['telefone']) : '—' ?></td>
                        <td><?= !empty($f['email']) ? htmlspecialchars((string) $f['email']) : '—' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime((string) $f['created_at'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-gold" onclick="visualizarFornecedor(<?= (int) $f['id'] ?>)" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="editarFornecedor(<?= (int) $f['id'] ?>)" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="excluirFornecedor(<?= (int) $f['id'] ?>, '<?= htmlspecialchars((string) $f['nome']) ?>')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para Formulário (será carregado via JS) -->
<div class="modal fade" id="modalFornecedor" tabindex="-1" aria-labelledby="modalFornecedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom" style="border-bottom-color: var(--border) !important;">
                <h5 class="modal-title" id="modalFornecedorLabel">
                    <i class="bi bi-shop me-2" style="color: var(--gold-soft);"></i>
                    <span class="text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">Novo Fornecedor</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(1.2);"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Formulário será carregado aqui -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para Visualização -->
<div class="modal fade" id="modalVisualizacao" tabindex="-1" aria-labelledby="modalVisualizacaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom" style="border-bottom-color: var(--border) !important;">
                <h5 class="modal-title" id="modalVisualizacaoLabel">
                    <i class="bi bi-eye me-2" style="color: var(--gold-soft);"></i>
                    <span class="text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">Detalhes do Fornecedor</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(1.2);"></button>
            </div>
            <div class="modal-body" id="modalVisualizacaoBody">
                <!-- Detalhes serão carregados aqui -->
            </div>
            <div class="modal-footer border-top" style="border-top-color: var(--border) !important;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?= $csrf ?>';

function abrirFormulario(id) {
    const titulo = id ? 'Editar Fornecedor' : 'Novo Fornecedor';
    document.getElementById('modalFornecedorLabel').textContent = titulo;
    
    if (id) {
        fetch(`/admin/fornecedores/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
            });
    } else {
        fetch('/admin/fornecedores/create')
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
            });
    }
}

function editarFornecedor(id) {
    abrirFormulario(id);
}

function visualizarFornecedor(id) {
    fetch(`/admin/fornecedores/${id}/view`)
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                const f = data.fornecedor;
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Código:</strong><br>
                            #${f.id}
                        </div>
                        <div class="col-md-6">
                            <strong>Nome:</strong><br>
                            ${f.nome}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>CNPJ:</strong><br>
                            ${f.cnpj || '—'}
                        </div>
                        <div class="col-md-6">
                            <strong>Contato:</strong><br>
                            ${f.contato || '—'}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Telefone:</strong><br>
                            ${f.telefone || '—'}
                        </div>
                        <div class="col-md-6">
                            <strong>E-mail:</strong><br>
                            ${f.email || '—'}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Data de Cadastro:</strong><br>
                            ${new Date(f.created_at).toLocaleDateString('pt-BR', {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}
                        </div>
                        <div class="col-md-6">
                            <strong>Última Atualização:</strong><br>
                            ${new Date(f.updated_at).toLocaleDateString('pt-BR', {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                `;
                document.getElementById('modalVisualizacaoBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalVisualizacao')).show();
            }
        });
}

function excluirFornecedor(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o fornecedor "${nome}"?`)) {
        fetch('/admin/fornecedores/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _csrf: csrfToken,
                id: id
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                alert('Fornecedor excluído com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.erro);
            }
        })
        .catch(err => alert('Erro ao excluir fornecedor.'));
    }
}

function aplicarFiltros() {
    const mes = document.getElementById('filtroMes').value;
    const ano = document.getElementById('filtroAno').value;
    let url = '/admin/fornecedores';
    const params = [];
    
    if (mes) params.push(`mes=${mes}`);
    if (ano) params.push(`ano=${ano}`);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

function limparFiltros() {
    window.location.href = '/admin/fornecedores';
}
</script>

<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
