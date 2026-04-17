<?php

declare(strict_types=1);

$publicPath = __DIR__ . '/public';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestedFile = realpath($publicPath . $requestPath);

if (
    $requestPath !== '/'
    && $requestedFile !== false
    && str_starts_with($requestedFile, $publicPath)
    && is_file($requestedFile)
) {
    return false;
}

require $publicPath . '/index.php';