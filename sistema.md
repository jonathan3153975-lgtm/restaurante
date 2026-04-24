# 🍽️ Sistema Completo de Gestão para Restaurante (SaaS)
Nome: Tech-Food
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
  - Bootstrap (UI responsiva, estilo customizado)
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
## Layout
Crie um tema customizado baseado em Bootstrap 5 com foco em um design moderno, elegante e minimalista utilizando uma paleta de cores escura com detalhes dourados.

Requisitos gerais do layout:

* Fundo principal preto (#000000)
* Textos principais em branco (#FFFFFF)
* Elementos de destaque (bordas, botões, links, detalhes) em dourado (#D4AF37 e variações mais claras como #F5D76E)
* Tons de cinza escuro para componentes secundários (#1E1E1E, #2A2A2A, #3A3A3A)

Estrutura técnica:

* Utilize variáveis CSS (:root) para definir toda a paleta de cores
* Sobrescreva as variáveis padrão do Bootstrap sempre que possível
* Crie um arquivo CSS organizado e reutilizável
* Evite uso excessivo de !important (usar apenas quando necessário)

Customizações obrigatórias:

1. Body:

* Fundo preto
* Texto branco

2. Cards, Navbar e Dropdowns:

* Fundo cinza escuro (#1E1E1E)
* Bordas douradas finas
* Aparência sofisticada e limpa

3. Botões:

* Botão primário com fundo dourado e texto preto
* Hover com dourado mais claro
* Versão outline com borda dourada e hover preenchido
* Bordas levemente arredondadas

4. Inputs e Selects:

* Fundo cinza escuro (#2A2A2A)
* Texto branco
* Borda dourada
* Placeholder em cinza claro
* Estado focus com glow dourado suave (box-shadow)

5. Modais:

* Fundo cinza escuro (#1E1E1E)
* Texto branco
* Bordas douradas
* Header e footer com separadores discretos dourados

6. Tabelas:

* Texto branco
* Cabeçalho com fundo cinza médio
* Linhas com separadores dourados
* Hover com leve destaque em cinza

7. Links:

* Cor dourada padrão
* Hover com dourado mais claro

8. Navbar:

* Fundo preto ou cinza muito escuro
* Links em branco
* Hover dourado

Extras desejáveis:

* Transições suaves (0.2s a 0.3s)
* Aparência premium (estilo “luxo”)
* Código limpo, bem organizado e comentado
* Compatível com Bootstrap 5 sem quebrar componentes nativos

Saída esperada:

* Um arquivo CSS completo
* Opcional: exemplo de HTML demonstrando os componentes estilizados (botões, inputs, modal, tabela e navbar)

Crie o ambiente inicial, com tela de login, simulando um usuário do banco de dados (inexistente ainda) e acessando o sistema, com menu simulado para validarmos o estilo
