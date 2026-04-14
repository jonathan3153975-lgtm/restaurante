<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Não Encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <h1 class="display-5">404</h1>
    <p class="lead">Rota não encontrada: <?= htmlspecialchars($path ?? '/', ENT_QUOTES, 'UTF-8') ?></p>
    <a href="/dashboard" class="btn btn-primary">Voltar ao painel</a>
</div>
</body>
</html>
