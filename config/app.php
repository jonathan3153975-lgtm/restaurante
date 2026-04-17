<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'Tech-Food',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'timezone' => 'America/Sao_Paulo',
    'currency' => 'BRL',
    'upload_dir' => dirname(__DIR__) . '/public/uploads',
];
