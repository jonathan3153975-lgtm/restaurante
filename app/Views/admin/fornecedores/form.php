<?php
$isEdicao = $fornecedor !== null;
$id = $isEdicao ? (int) $fornecedor['id'] : 0;
$nome = $isEdicao ? htmlspecialchars((string) $fornecedor['nome']) : '';
$cnpj = $isEdicao ? htmlspecialchars((string) $fornecedor['cnpj']) : '';
$contato = $isEdicao ? htmlspecialchars((string) $fornecedor['contato']) : '';
$telefone = $isEdicao ? htmlspecialchars((string) $fornecedor['telefone']) : '';
$email = $isEdicao ? htmlspecialchars((string) $fornecedor['email']) : '';

if ($isEdicao) {
    echo '<!-- Formulário de Edição -->';
} else {
    echo '<!-- Formulário de Criação -->';
}
?>

<form id="formFornecedor" method="POST" action="<?= $isEdicao ? '/admin/fornecedores/update' : '/admin/fornecedores/store' ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <?php if ($isEdicao): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label for="nome" class="form-label">
            <i class="bi bi-shop me-1"></i>Nome da Empresa *
        </label>
        <input 
            type="text" 
            class="form-control" 
            id="nome" 
            name="nome" 
            placeholder="Razão social ou nome fantasia"
            value="<?= $nome ?>"
            required
            minlength="3"
            maxlength="160"
        >
        <small class="form-text text-muted">Mínimo 3 caracteres</small>
        <div id="erro-nome" class="alert alert-danger mt-2" style="display: none;"></div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="cnpj" class="form-label">
                    <i class="bi bi-file-text me-1"></i>CNPJ
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="cnpj" 
                    name="cnpj" 
                    placeholder="00.000.000/0000-00"
                    value="<?= $cnpj ?>"
                    maxlength="18"
                    data-mask="cnpj"
                >
                <small class="form-text text-muted">Formato: 00.000.000/0000-00 (opcional)</small>
                <div id="erro-cnpj" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-1"></i>E-mail
                </label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    placeholder="contato@fornecedor.com.br"
                    value="<?= $email ?>"
                    maxlength="160"
                >
                <small class="form-text text-muted">E-mail para contato (opcional)</small>
                <div id="erro-email" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="contato" class="form-label">
                    <i class="bi bi-person me-1"></i>Pessoa de Contato
                </label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="contato" 
                    name="contato" 
                    placeholder="Nome da pessoa responsável"
                    value="<?= $contato ?>"
                    maxlength="120"
                >
                <small class="form-text text-muted">Opcional</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="telefone" class="form-label">
                    <i class="bi bi-telephone me-1"></i>Telefone
                </label>
                <input 
                    type="tel" 
                    class="form-control" 
                    id="telefone" 
                    name="telefone" 
                    placeholder="(00) 0000-0000 ou (00) 00000-0000"
                    value="<?= $telefone ?>"
                    maxlength="16"
                    data-mask="telephone"
                >
                <small class="form-text text-muted">Formato: (00) 0000-0000 ou (00) 00000-0000 (opcional)</small>
                <div id="erro-telefone" class="alert alert-danger mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-check-circle me-1"></i><?= $isEdicao ? 'Salvar Alterações' : 'Cadastrar Fornecedor' ?>
        </button>
    </div>
</form>

<script>
document.getElementById('formFornecedor').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Limpar erros anteriores
    document.querySelectorAll('[id^="erro-"]').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });

    const formData = new FormData(document.getElementById('formFornecedor'));
    const dados = Object.fromEntries(formData);

    try {
        const response = await fetch(document.getElementById('formFornecedor').action, {
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
