# Restaurante SaaS

Projeto base em PHP 8 com arquitetura MVC multi-tenant para gestao de restaurante.

## Como executar

1. Instale dependencias:

```bash
composer install
```

2. Execute servidor local:

```bash
php -S localhost:8000 -t public
```

3. Acesse:

- Login: http://localhost:8000/login
- Usuario demo: admin@restaurante.local
- Senha demo: 123456

## Testes

```bash
composer test
```

## Banco de dados

Schema inicial completo em `database/schema.sql`.
