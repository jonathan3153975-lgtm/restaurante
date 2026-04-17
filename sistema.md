# 🍽️ Sistema Completo de Gestão para Restaurante (SaaS)

## 📌 Visão Geral
Sistema completo para gestão de restaurantes em modelo **SaaS multi-tenant**, contemplando administração, contabilidade, estoque, cardápio digital com pedidos via QR-Code, pagamento da conta pelo acesso do cliente com confirmação no caixa, controle de mesas, caixa, funcionários e integração fiscal e de pagamentos.

O objetivo é fornecer uma solução **robusta, escalável, segura e moderna**, com resposta em tempo real entre pedidos do cliente na mesa e o painel operacional do restaurante.

---

## 🧱 Tecnologias Obrigatórias

- **Backend:** PHP 8+ (POO, MVC, Clean Code)
- **Banco de Dados:** MySQL 8 (PDO)
- **Frontend:** HTML5, CSS3, JavaScript
- **Bibliotecas:**
  - jQuery
  - Bootstrap (UI responsiva)
  - SweetAlert (alertas e confirmações)
  - Quill (editor de textos ricos)
- **Arquitetura:** MVC + SaaS Multi-Tenant
- **Pagamentos:** Mercado Pago (Pix, Crédito e Débito)

---

## 🏗️ Arquitetura do Sistema

- Estrutura SaaS com isolamento lógico por tenant (empresa/restaurante)
- Código limpo e organizado (Clean Code)
- Separação clara de camadas:
  - Controllers
  - Models
  - Views
- Padrões:
  - Repository Pattern
  - Service Layer
- Segurança:
  - Hash de senha com `password_hash()`
  - Proteção contra SQL Injection (PDO + BindParams)
  - Validação e sanitização de inputs
  - Controle de sessão e CSRF

---

## 👤 Módulo de Autenticação e Acesso

- Login e logout
- Controle de perfis:
  - Administrador
  - Gerente
  - Caixa
  - Cozinha
  - Copa
- Permissões granulares por módulo
- Recuperação de senha

---

## 🧾 Módulo Administrativo
O menu lateral com submenus, na seguinte estrutura:
  Menu Administrativo;
  Submenu:
    - Cadastro de fornecedores;
    - NF entrada (Lançamento manual e importação xml);

      -> Esse módulo deve ter: 
        1 - Painel com campo de busca dinâmico (digitar e filtrar), além de filtros por mês e ano e listar as notas registradas (inclua paginação nos registros) Com opção para visualizar (em modal ou modo documento em outra aba para impressão), editar e excluir.

        2 - No cadastro com campos específicos para o fim, incluíndo impostos. O ideal é que caso seja nota fiscal de produtos referentes ao estoque, já inclua o saldo no estoque automaticamente. Inclua as máscaras referentes aos campos, como cnpj, por exemplo. Para buscar o fornecedor, utilize um campo digitável que retorne o registro do fornecedor. Nos campos referentes a valores, utilize uma formatação que insira a vírgula duas casas após, automaticamente.

        3 - O formulário de cadastro também deve ter um atalho para cadastrar fornecedor, de maneira rápida, para que o usuário não saia da tela de lançamento de notas.
        
        4 - 

    - Estoque;
  Menu RH;
  Submenu:
    - Funcionários;
    - Holerites;

### 📥 Notas Fiscais de Entrada
- Lançamento manual e importação XML
- Fornecedor
- Produtos vinculados ao estoque
- Impostos (ICMS, IPI, PIS, COFINS)
- Centros de custo

### 📦 Controle de Estoque
- Cadastro de produtos/matérias-primas
- Unidades de medida
- Estoque mínimo
- Entrada automática via NF
- Saída automática por venda
- Ajustes manuais

### 📤 Saída de Materiais
- Consumo interno
- Perdas e desperdícios
- Registro com responsável

---

## 👨‍🍳 Funcionários e RH

- Cadastro de funcionários
- Cargos e salários
- Controle de jornada
- Benefícios
- Descontos
- Histórico salarial
- Relatórios de folha

---

## 💰 Financeiro e Caixa

### 🧾 Controle de Caixa
- Abertura e fechamento
- Entradas e saídas
- Sangria e reforço
- Caixa por usuário

### 📊 Movimentações Financeiras
- Contas a pagar
- Contas a receber
- Categorias financeiras
- Fluxo de caixa
- Relatórios por período

---

## 🍔 Cardápio e Produtos

### 📂 Categorias
- Criar, editar e excluir
- Ordenação
- Ativação/Inativação

### 🍽️ Itens do Cardápio
- Nome, descrição e imagem
- Preço
- Variações (tamanho, adicional)
- Vinculação com estoque
- Disponibilidade

---

## 🪑 Mesas e Pedidos (Cardápio Digital)

### 📱 Cardápio Digital via QR-Code
- QR-Code único por mesa
- Acesso sem login
- Interface mobile first
- Atualização em tempo real

### 🛒 Pedido do Cliente
- Seleção de itens
- Observações
- Confirmação do pedido
- Integração imediata com cozinha

### 💳 Pagamento pelo Smartphone
- Pix
- Cartão de crédito
- Cartão de débito
- Confirmação automática

---

## 🍳 Painel da Cozinha

- Recebimento de pedidos em tempo real
- Status:
  - Pendente
  - Em preparo
  - Concluído
- Alertas sonoros e visuais

---

## 🧾 Integração Fiscal (SEFAZ)

- Emissão automática de NFC-e / NF-e
- Integração via API certificada
- Certificado digital A1
- Contingência
- Cancelamento e inutilização
- Download de XML e DANFE

---

## 🔗 Integrações Externas

- SEFAZ (NF-e / NFC-e)
- Mercado Pago (Pix, Crédito e Débito)
- Webhooks para pagamentos

---

## 🎨 UI/UX

- Design moderno e profissional
- Linhas retas e minimalistas
- Layout responsivo
- Feedback imediato de ações
- Alta performance

---

## 📡 Tempo Real

- Comunicação instantânea:
  - Pedido → Cozinha
  - Pedido → Caixa
- Uso de:
  - WebSockets ou Long Polling

---

## 📊 Relatórios

- Vendas
- Produtos mais vendidos
- Estoque
- Financeiro
- Funcionários
- Filtros por período
- Exportação PDF/Excel

---

## ⚙️ Requisitos Não Funcionais

- Escalabilidade
- Alta disponibilidade
- Backup automático
- Logs e auditoria
- LGPD

---

## 🚀 Entregáveis

- Código-fonte organizado
- Banco de dados versionado
- Documentação técnica
- Manual do usuário

---

## ✅ Considerações Finais

Este sistema deve ser **modular**, preparado para futuras expansões, como aplicativos mobile, integrações adicionais e BI avançado, mantendo sempre boas práticas de desenvolvimento e segurança.
