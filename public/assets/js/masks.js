/**
 * Funções de máscara de input para formatação de dados brasileiros
 */

/**
 * Aplicar máscara de CPF: 000.000.000-00
 */
function maskCPF(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length > 9) {
            value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6, 9) + '-' + value.slice(9);
        } else if (value.length > 6) {
            value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6);
        } else if (value.length > 3) {
            value = value.slice(0, 3) + '.' + value.slice(3);
        }
        e.target.value = value;
    });
}

/**
 * Aplicar máscara de CNPJ: 00.000.000/0000-00
 */
function maskCNPJ(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 14) value = value.slice(0, 14);
        
        if (value.length > 8) {
            value = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5, 8) + '/' + value.slice(8, 12) + '-' + value.slice(12);
        } else if (value.length > 5) {
            value = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5);
        } else if (value.length > 2) {
            value = value.slice(0, 2) + '.' + value.slice(2);
        }
        e.target.value = value;
    });
}

/**
 * Aplicar máscara de Telefone: (00) 0000-0000 ou (00) 00000-0000
 */
function maskTelephone(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length > 7) {
            if (value.length === 11) {
                // (00) 00000-0000 para celular
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
            } else if (value.length === 10) {
                // (00) 0000-0000 para fixo
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 6) + '-' + value.slice(6);
            }
        } else if (value.length > 2) {
            value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
        }
        e.target.value = value;
    });
}

/**
 * Aplicar máscara de valor monetário em formato brasileiro: 1.234,56
 */
function maskCurrency(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove tudo que não é número
        value = value.replace(/\D/g, '');
        
        if (value.length === 0) {
            e.target.value = '';
            return;
        }
        
        // Garante mínimo de 1 dígito (para 0,00)
        if (value.length === 1) {
            value = '0' + value;
        }
        
        // Últimos 2 dígitos são centavos
        let decimal = value.slice(-2);
        let inteira = value.slice(0, -2);
        
        // Remove zeros à esquerda da parte inteira
        inteira = inteira.replace(/^0+/, '') || '0';
        
        // Adiciona pontos de separador de milhar
        inteira = inteira.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        e.target.value = inteira + ',' + decimal;
    });
    
    // Garantir que ao sair do campo, tenha pelo menos ,00
    input.addEventListener('blur', function(e) {
        let value = e.target.value;
        if (value && !value.includes(',')) {
            value = value.replace(/\D/g, '');
            if (value) {
                if (value.length === 1) {
                    value = '0' + value;
                }
                let decimal = value.slice(-2);
                let inteira = value.slice(0, -2);
                inteira = inteira.replace(/^0+/, '') || '0';
                inteira = inteira.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                e.target.value = inteira + ',' + decimal;
            }
        }
    });
}

/**
 * Adicionar máscaras a um formulário por data-mask attribute
 * data-mask="cpf" | data-mask="cnpj" | data-mask="telephone" | data-mask="currency"
 */
function applyMasks() {
    document.querySelectorAll('[data-mask="cpf"]').forEach(input => maskCPF(input));
    document.querySelectorAll('[data-mask="cnpj"]').forEach(input => maskCNPJ(input));
    document.querySelectorAll('[data-mask="telephone"]').forEach(input => maskTelephone(input));
    document.querySelectorAll('[data-mask="currency"]').forEach(input => maskCurrency(input));
}

// Aplicar máscaras quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', applyMasks);
// Também executar para conteúdo carregado dinamicamente
const observer = new MutationObserver(applyMasks);
observer.observe(document.body, { childList: true, subtree: true });
