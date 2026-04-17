# Tech-Food

Sistema de administração de restaurante em PHP puro, seguindo MVC, PDO e layout responsivo sem framework CSS.

## O que foi entregue

- Login administrativo com perfis `admin`, `manager` e `cashier`
- Dashboard com indicadores operacionais
- Módulo de cardápio com categorias, cadastro de item, imagem, estoque e preview do cliente
- Módulo de mesas com QR-Code, ocupação manual e encerramento rápido
- Módulo de pedidos priorizados por fila
- Módulo de caixa com fechamento por Pix, crédito, débito e dinheiro
- Fluxo do cliente por mesa com registro via SweetAlert, seleção de itens, remoções, adicionais, subtotal e solicitação de pagamento
- Tema claro/escuro persistido em `localStorage`
- Banco MariaDB versionado com migrations

## Estrutura

```text
public/
src/
views/
config/
database/
scripts/
```

## Instalação

1. Copie `.env.example` para `.env` e ajuste as credenciais do MariaDB.
2. Crie o banco com o script abaixo:

```sql
SOURCE database/create_database.sql;
```

3. Execute as migrations:

```bash
php scripts/migrate.php
```

4. Suba o servidor embutido do PHP:

```bash
php -S localhost:8000 -t public
```

5. Acesse `http://localhost:8000`.

## Credenciais iniciais

- Admin: `admin@techfood.local` / `admin123`
- Gerente: `gerente@techfood.local` / `admin123`
- Caixa: `caixa@techfood.local` / `admin123`

## Observações

- O QR-Code usa serviço externo de imagem para gerar o código visual a partir da URL da mesa.
- Cada alteração futura no banco deve gerar um novo arquivo em `database/migrations`.
- O MVP foi estruturado para expansão posterior de estoque avançado, fiscal e integrações de pagamento reais.
