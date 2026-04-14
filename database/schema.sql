CREATE DATABASE IF NOT EXISTS restaurante_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE restaurante_saas;

CREATE TABLE tenants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_fantasia VARCHAR(120) NOT NULL,
    razao_social VARCHAR(160) NOT NULL,
    cnpj VARCHAR(14) NOT NULL,
    email VARCHAR(160) NOT NULL,
    telefone VARCHAR(11) NULL,
    status ENUM('ativo', 'inativo', 'suspenso') NOT NULL DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tenants_cnpj (cnpj)
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('administrador', 'gerente', 'caixa', 'cozinha') NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_email_tenant (tenant_id, email),
    CONSTRAINT fk_users_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE permissoes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(120) NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL
);

CREATE TABLE user_permissoes (
    user_id BIGINT UNSIGNED NOT NULL,
    permissao_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, permissao_id),
    CONSTRAINT fk_user_perm_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_user_perm_perm FOREIGN KEY (permissao_id) REFERENCES permissoes(id)
);

CREATE TABLE fornecedores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(160) NOT NULL,
    cnpj VARCHAR(14) NULL,
    contato VARCHAR(120) NULL,
    telefone VARCHAR(11) NULL,
    email VARCHAR(160) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_fornecedor_tenant_nome (tenant_id, nome),
    CONSTRAINT fk_fornecedor_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE centros_custo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NULL,
    INDEX idx_cc_tenant_nome (tenant_id, nome),
    CONSTRAINT fk_cc_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE produtos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(40) NOT NULL,
    nome VARCHAR(160) NOT NULL,
    tipo ENUM('insumo', 'item_cardapio') NOT NULL,
    unidade_medida VARCHAR(20) NOT NULL,
    estoque_minimo DECIMAL(10,3) DEFAULT 0,
    saldo_atual DECIMAL(10,3) DEFAULT 0,
    preco_venda DECIMAL(12,2) NULL,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_produto_codigo_tenant (tenant_id, codigo),
    INDEX idx_produto_tenant_nome (tenant_id, nome),
    CONSTRAINT fk_produto_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE notas_entrada (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    fornecedor_id BIGINT UNSIGNED NOT NULL,
    numero_nf VARCHAR(60) NOT NULL,
    serie VARCHAR(10) NULL,
    data_emissao DATE NOT NULL,
    valor_total DECIMAL(12,2) NOT NULL,
    icms DECIMAL(12,2) DEFAULT 0,
    ipi DECIMAL(12,2) DEFAULT 0,
    pis DECIMAL(12,2) DEFAULT 0,
    cofins DECIMAL(12,2) DEFAULT 0,
    centro_custo_id BIGINT UNSIGNED NULL,
    xml_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nf_tenant_numero (tenant_id, numero_nf),
    CONSTRAINT fk_nf_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_nf_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id),
    CONSTRAINT fk_nf_cc FOREIGN KEY (centro_custo_id) REFERENCES centros_custo(id)
);

CREATE TABLE nota_entrada_itens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nota_entrada_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL,
    valor_unitario DECIMAL(12,2) NOT NULL,
    valor_total DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_nfi_nf FOREIGN KEY (nota_entrada_id) REFERENCES notas_entrada(id),
    CONSTRAINT fk_nfi_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE estoque_movimentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('entrada_nf', 'saida_venda', 'ajuste', 'consumo_interno', 'perda') NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL,
    observacao VARCHAR(255) NULL,
    responsavel_user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estoque_tenant_produto (tenant_id, produto_id),
    CONSTRAINT fk_estoque_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) REFERENCES produtos(id),
    CONSTRAINT fk_estoque_resp FOREIGN KEY (responsavel_user_id) REFERENCES users(id)
);

CREATE TABLE funcionarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    telefone VARCHAR(11) NULL,
    email VARCHAR(160) NULL,
    cargo VARCHAR(100) NOT NULL,
    salario_base DECIMAL(12,2) NOT NULL,
    data_admissao DATE NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_funcionario_cpf_tenant (tenant_id, cpf),
    INDEX idx_func_tenant_nome (tenant_id, nome),
    CONSTRAINT fk_funcionario_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE jornadas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    funcionario_id BIGINT UNSIGNED NOT NULL,
    data_referencia DATE NOT NULL,
    entrada DATETIME NULL,
    saida DATETIME NULL,
    minutos_pausa INT DEFAULT 0,
    INDEX idx_jornada_tenant_data (tenant_id, data_referencia),
    CONSTRAINT fk_jornada_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_jornada_funcionario FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

CREATE TABLE beneficios_funcionario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    funcionario_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_beneficio_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_beneficio_funcionario FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

CREATE TABLE descontos_funcionario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    funcionario_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_desconto_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_desconto_funcionario FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

CREATE TABLE caixas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    data_operacao DATE NOT NULL,
    aberto_em DATETIME NOT NULL,
    fechado_em DATETIME NULL,
    saldo_inicial DECIMAL(12,2) NOT NULL DEFAULT 0,
    saldo_final DECIMAL(12,2) NULL,
    status ENUM('aberto', 'fechado') NOT NULL DEFAULT 'aberto',
    INDEX idx_caixa_tenant_data (tenant_id, data_operacao),
    CONSTRAINT fk_caixa_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_caixa_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE caixa_movimentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    caixa_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('entrada', 'saida', 'sangria', 'reforco') NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    observacao VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_caixa_mov_tenant (tenant_id, caixa_id),
    CONSTRAINT fk_caixa_mov_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_caixa_mov_caixa FOREIGN KEY (caixa_id) REFERENCES caixas(id)
);

CREATE TABLE contas_pagar (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    fornecedor_id BIGINT UNSIGNED NULL,
    descricao VARCHAR(160) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    vencimento DATE NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    status ENUM('pendente', 'pago', 'vencido') NOT NULL DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cp_tenant_venc (tenant_id, vencimento),
    CONSTRAINT fk_cp_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_cp_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

CREATE TABLE contas_receber (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    descricao VARCHAR(160) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    vencimento DATE NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    status ENUM('pendente', 'recebido', 'vencido') NOT NULL DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cr_tenant_venc (tenant_id, vencimento),
    CONSTRAINT fk_cr_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE categorias_cardapio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(100) NOT NULL,
    ordem_exibicao INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_categoria_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    UNIQUE KEY uk_categoria_nome_tenant (tenant_id, nome)
);

CREATE TABLE itens_cardapio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    categoria_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NULL,
    nome VARCHAR(160) NOT NULL,
    descricao TEXT NULL,
    imagem_url VARCHAR(255) NULL,
    preco_base DECIMAL(12,2) NOT NULL,
    disponivel TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_tenant_nome (tenant_id, nome),
    CONSTRAINT fk_item_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_item_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_cardapio(id),
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE item_variacoes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_cardapio_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    adicional_preco DECIMAL(12,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_variacao_item FOREIGN KEY (item_cardapio_id) REFERENCES itens_cardapio(id)
);

CREATE TABLE mesas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(20) NOT NULL,
    qr_token CHAR(36) NOT NULL,
    status ENUM('livre', 'ocupada', 'fechamento') NOT NULL DEFAULT 'livre',
    UNIQUE KEY uk_mesa_codigo_tenant (tenant_id, codigo),
    UNIQUE KEY uk_mesa_qr_token (qr_token),
    CONSTRAINT fk_mesa_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    mesa_id BIGINT UNSIGNED NULL,
    codigo VARCHAR(30) NOT NULL,
    canal ENUM('mesa_qr', 'balcao', 'delivery') NOT NULL,
    status ENUM('pendente', 'em_preparo', 'concluido', 'cancelado', 'pago') NOT NULL DEFAULT 'pendente',
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    observacao TEXT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pedido_tenant_status (tenant_id, status),
    UNIQUE KEY uk_pedido_codigo_tenant (tenant_id, codigo),
    CONSTRAINT fk_pedido_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_pedido_mesa FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

CREATE TABLE pedido_itens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    item_cardapio_id BIGINT UNSIGNED NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(12,2) NOT NULL,
    observacao VARCHAR(255) NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_pi_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    CONSTRAINT fk_pi_item FOREIGN KEY (item_cardapio_id) REFERENCES itens_cardapio(id)
);

CREATE TABLE pagamentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    gateway ENUM('mercado_pago') NOT NULL,
    tipo ENUM('pix', 'credito', 'debito') NOT NULL,
    status ENUM('pendente', 'aprovado', 'recusado', 'cancelado') NOT NULL DEFAULT 'pendente',
    valor DECIMAL(12,2) NOT NULL,
    external_id VARCHAR(100) NULL,
    payload JSON NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pag_tenant_status (tenant_id, status),
    CONSTRAINT fk_pag_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_pag_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

CREATE TABLE webhook_eventos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    origem ENUM('mercado_pago') NOT NULL,
    event_id VARCHAR(120) NOT NULL,
    tipo VARCHAR(120) NOT NULL,
    payload JSON NOT NULL,
    processado_em DATETIME NULL,
    status ENUM('novo', 'processado', 'erro') NOT NULL DEFAULT 'novo',
    UNIQUE KEY uk_webhook_event (origem, event_id),
    CONSTRAINT fk_webhook_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

CREATE TABLE documentos_fiscais (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NULL,
    tipo ENUM('NFCe', 'NFe') NOT NULL,
    numero VARCHAR(60) NOT NULL,
    chave_acesso VARCHAR(44) NULL,
    status ENUM('autorizado', 'rejeitado', 'contingencia', 'cancelado', 'inutilizado') NOT NULL,
    xml_path VARCHAR(255) NULL,
    danfe_path VARCHAR(255) NULL,
    protocolo VARCHAR(60) NULL,
    motivo_rejeicao VARCHAR(255) NULL,
    emitido_em DATETIME NULL,
    INDEX idx_doc_tenant_tipo_status (tenant_id, tipo, status),
    CONSTRAINT fk_doc_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_doc_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

CREATE TABLE auditoria_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    modulo VARCHAR(100) NOT NULL,
    acao VARCHAR(100) NOT NULL,
    recurso VARCHAR(100) NOT NULL,
    recurso_id VARCHAR(100) NULL,
    antes JSON NULL,
    depois JSON NULL,
    ip_origem VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_auditoria_tenant_modulo (tenant_id, modulo),
    CONSTRAINT fk_auditoria_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    CONSTRAINT fk_auditoria_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE jobs_fila (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NULL,
    fila VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    tentativas INT NOT NULL DEFAULT 0,
    disponivel_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reservado_em DATETIME NULL,
    concluido_em DATETIME NULL,
    status ENUM('pendente', 'reservado', 'concluido', 'falha') NOT NULL DEFAULT 'pendente',
    INDEX idx_jobs_status (status, disponivel_em)
);
