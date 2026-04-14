<?php
$title = 'Notas Fiscais de Entrada';
ob_start();
?>
<h1 class="h4 mb-3 module-title"><i class="bi bi-receipt-cutoff me-2"></i>Notas Fiscais de Entrada</h1>

<!-- Action Toolbar -->
<div class="action-toolbar mb-3">
    <button class="btn btn-outline-gold btn-sm" type="button" onclick="abrirFormulario(null)">
        <i class="bi bi-plus-circle me-1"></i>Nova Nota
    </button>
    <a class="btn btn-outline-info btn-sm" href="/admin/fornecedores" title="Gerenciar fornecedores">
        <i class="bi bi-shop me-1"></i>Fornecedores
    </a>
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
        <input type="text" class="form-control" placeholder="Filtrar por número ou fornecedor..." data-filter-input="#tabela-notas" value="<?= htmlspecialchars($filtroTexto) ?>">
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

<!-- Tabela de Notas de Entrada -->
<div class="table-responsive">
    <table class="table table-striped" id="tabela-notas" data-filter-table>
        <thead>
            <tr>
                <th>Número NF</th>
                <th>Série</th>
                <th>Fornecedor</th>
                <th>Data de Faturamento</th>
                <th>Data de Entrada</th>
                <th>Valor Total</th>
                <th>Centro de Custo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($notasEntrada)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                        Nenhuma nota fiscal encontrada.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($notasEntrada as $nota): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars((string) $nota['numero_nf']) ?></strong></td>
                        <td><?= !empty($nota['serie']) ? htmlspecialchars((string) $nota['serie']) : '—' ?></td>
                        <td><?= htmlspecialchars((string) $nota['fornecedor_nome']) ?></td>
                        <td><?= date('d/m/Y', strtotime((string) $nota['data_emissao'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime((string) $nota['created_at'])) ?></td>
                        <td><strong>R$ <?= number_format((float) $nota['valor_total'], 2, ',', '.') ?></strong></td>
                        <td><?= !empty($nota['centro_custo_id']) ? htmlspecialchars((string) $nota['centro_custo_id']) : '—' ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-gold" onclick="visualizarNota(<?= (int) $nota['id'] ?>)" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="editarNota(<?= (int) $nota['id'] ?>)" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="excluirNota(<?= (int) $nota['id'] ?>, '<?= htmlspecialchars((string) $nota['numero_nf']) ?>')" title="Excluir">
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

<!-- Modal para Formulário -->
<div class="modal fade" id="modalNotaEntrada" tabindex="-1" aria-labelledby="modalNotaEntradaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom" style="border-bottom-color: var(--border) !important;">
                <h5 class="modal-title" id="modalNotaEntradaLabel">
                    <i class="bi bi-receipt me-2" style="color: var(--gold-soft);"></i>
                    <span class="text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">Nova Nota Fiscal</span>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom" style="border-bottom-color: var(--border) !important;">
                <h5 class="modal-title" id="modalVisualizacaoLabel">
                    <i class="bi bi-eye me-2" style="color: var(--gold-soft);"></i>
                    <span class="text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">Detalhes da Nota Fiscal</span>
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
    const titulo = id ? 'Editar Nota Fiscal' : 'Nova Nota Fiscal';
    document.getElementById('modalNotaEntradaLabel').textContent = titulo;
    
    if (id) {
        fetch(`/admin/notas-entrada/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalNotaEntrada')).show();
            });
    } else {
        fetch('/admin/notas-entrada/create')
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalNotaEntrada')).show();
            });
    }
}

function editarNota(id) {
    abrirFormulario(id);
}

function visualizarNota(id) {
    fetch(`/admin/notas-entrada/${id}/view`)
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                const n = data.notaEntrada;
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Número NF:</strong><br>
                            ${n.numero_nf}
                        </div>
                        <div class="col-md-6">
                            <strong>Série:</strong><br>
                            ${n.serie || '—'}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fornecedor:</strong><br>
                            ${n.fornecedor_nome}
                        </div>
                        <div class="col-md-6">
                            <strong>Data de Faturamento:</strong><br>
                            ${new Date(n.data_emissao).toLocaleDateString('pt-BR')}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Centro de Custo:</strong><br>
                            ${n.centro_custo_nome || '—'}
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Valor Total:</strong><br>
                            R$ ${parseFloat(n.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </div>
                        <div class="col-md-6">
                            <strong>ICMS:</strong><br>
                            R$ ${parseFloat(n.icms).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>IPI:</strong><br>
                            R$ ${parseFloat(n.ipi).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </div>
                        <div class="col-md-6">
                            <strong>PIS:</strong><br>
                            R$ ${parseFloat(n.pis).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>COFINS:</strong><br>
                            R$ ${parseFloat(n.cofins).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </div>
                        <div class="col-md-6">
                            <strong>Data de Entrada:</strong><br>
                            ${new Date(n.created_at).toLocaleDateString('pt-BR', {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                `;
                document.getElementById('modalVisualizacaoBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalVisualizacao')).show();
            }
        });
}

function excluirNota(id, numero) {
    if (confirm(`Tem certeza que deseja excluir a nota fiscal "${numero}"?`)) {
        fetch('/admin/notas-entrada/delete', {
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
                alert('Nota fiscal excluída com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.erro);
            }
        })
        .catch(err => alert('Erro ao excluir nota fiscal.'));
    }
}

function aplicarFiltros() {
    const mes = document.getElementById('filtroMes').value;
    const ano = document.getElementById('filtroAno').value;
    let url = '/admin/notas-entrada';
    const params = [];
    
    if (mes) params.push(`mes=${mes}`);
    if (ano) params.push(`ano=${ano}`);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

function limparFiltros() {
    window.location.href = '/admin/notas-entrada';
}
</script>

<?php
$content = (string) ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
