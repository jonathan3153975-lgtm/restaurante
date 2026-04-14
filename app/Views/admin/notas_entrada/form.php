<?php
$isEdicao = $notaEntrada !== null;
$id = $isEdicao ? (int) $notaEntrada['id'] : 0;
$numeroNf = $isEdicao ? htmlspecialchars((string) $notaEntrada['numero_nf']) : '';
$serie = $isEdicao ? htmlspecialchars((string) $notaEntrada['serie']) : '';
$fornecedorId = $isEdicao ? (int) $notaEntrada['fornecedor_id'] : 0;
$dataEmissao = $isEdicao ? htmlspecialchars((string) $notaEntrada['data_emissao']) : date('Y-m-d');
$valorTotal = $isEdicao ? number_format((float) $notaEntrada['valor_total'], 2, ',', '.') : '';
$icms = $isEdicao ? number_format((float) $notaEntrada['icms'], 2, ',', '.') : '0,00';
$ipi = $isEdicao ? number_format((float) $notaEntrada['ipi'], 2, ',', '.') : '0,00';
$pis = $isEdicao ? number_format((float) $notaEntrada['pis'], 2, ',', '.') : '0,00';
$cofins = $isEdicao ? number_format((float) $notaEntrada['cofins'], 2, ',', '.') : '0,00';
$centroCustoId = $isEdicao ? (int) $notaEntrada['centro_custo_id'] : 0;

if ($isEdicao) {
    echo '<!-- Formulário de Edição -->';
} else {
    echo '<!-- Formulário de Criação -->';
}
?>

<form id="formNotaEntrada" method="POST" action="<?= $isEdicao ? '/admin/notas-entrada/update' : '/admin/notas-entrada/store' ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <?php if ($isEdicao): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="numero_nf" class="form-label">
                    <i class="bi bi-receipt me-1"></i>Número NF *
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="numero_nf" 
                    name="numero_nf" 
                    placeholder="001234"
                    value="<?= $numeroNf ?>"
                    required
                    maxlength="60"
                >
                <div id="erro-numero_nf" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="serie" class="form-label">
                    <i class="bi bi-stack me-1"></i>Série
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="serie" 
                    name="serie" 
                    placeholder="A"
                    value="<?= $serie ?>"
                    maxlength="10"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fornecedor_id" class="form-label">
                    <i class="bi bi-shop me-1"></i>Fornecedor *
                </label>
                <select class="form-select" id="fornecedor_id" name="fornecedor_id" required>
                    <option value="">— Selecione um fornecedor</option>
                    <?php foreach ($fornecedores as $f): ?>
                        <option value="<?= (int) $f['id'] ?>" <?= $fornecedorId === (int) $f['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $f['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="erro-fornecedor_id" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="data_emissao" class="form-label">
                    <i class="bi bi-calendar2-event me-1"></i>Data de Faturamento *
                </label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="data_emissao" 
                    name="data_emissao" 
                    value="<?= $dataEmissao ?>"
                    required
                >
                <div id="erro-data_emissao" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="valor_total" class="form-label">
                    <i class="bi bi-coin me-1"></i>Valor Total (R$) *
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="valor_total" 
                    name="valor_total" 
                    placeholder="0,00"
                    value="<?= $valorTotal ?>"
                    required
                    data-mask="currency"
                >
                <div id="erro-valor_total" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="centro_custo_id" class="form-label">
                    <i class="bi bi-diagram-2 me-1"></i>Centro de Custo
                </label>
                <select class="form-select" id="centro_custo_id" name="centro_custo_id">
                    <option value="">— Nenhum</option>
                    <?php foreach ($centrosCusto as $cc): ?>
                        <option value="<?= (int) $cc['id'] ?>" <?= $centroCustoId === (int) $cc['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $cc['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <hr class="my-4">
    <h6 class="mb-3"><i class="bi bi-percent me-1"></i>Impostos</h6>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="icms" class="form-label">ICMS (R$)</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="icms" 
                    name="icms" 
                    placeholder="0,00"
                    value="<?= $icms ?>"
                    data-mask="currency"
                >
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="ipi" class="form-label">IPI (R$)</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="ipi" 
                    name="ipi" 
                    placeholder="0,00"
                    value="<?= $ipi ?>"
                    data-mask="currency"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="pis" class="form-label">PIS (R$)</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="pis" 
                    name="pis" 
                    placeholder="0,00"
                    value="<?= $pis ?>"
                    data-mask="currency"
                >
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="cofins" class="form-label">COFINS (R$)</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="cofins" 
                    name="cofins" 
                    placeholder="0,00"
                    value="<?= $cofins ?>"
                    data-mask="currency"
                >
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-check-circle me-1"></i><?= $isEdicao ? 'Salvar Alterações' : 'Registrar Nota Fiscal' ?>
        </button>
    </div>
</form>

<script>
document.getElementById('formNotaEntrada').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Limpar erros anteriores
    document.querySelectorAll('[id^="erro-"]').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });

    const formData = new FormData(document.getElementById('formNotaEntrada'));
    const dados = Object.fromEntries(formData);

    try {
        const response = await fetch(document.getElementById('formNotaEntrada').action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dados)
        });

        const result = await response.json();

        if (!result.sucesso) {
            if (result.erros) {
                Object.keys(result.erros).forEach(campo => {
                    const erroEl = document.getElementById(`erro-${campo}`);
                    if (erroEl) {
                        erroEl.textContent = result.erros[campo];
                        erroEl.style.display = 'block';
                    }
                });
            } else if (result.erro) {
                alert('Erro: ' + result.erro);
            }
        } else {
            alert(result.mensagem || 'Operação realizada com sucesso!');
            location.reload();
        }
    } catch (err) {
        alert('Erro ao processar formulário.');
        console.error(err);
    }
});
</script>
