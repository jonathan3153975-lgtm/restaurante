<?php

declare(strict_types=1);

use App\Core\Session;

function config(?string $key = null, mixed $default = null): mixed
{
    $config = $GLOBALS['app_config'] ?? [];

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function url(string $path = ''): string
{
    $baseUrl = rtrim((string) config('base_url', ''), '/');
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === '') {
        return $baseUrl === '' ? '/' : $baseUrl . '/';
    }

    return ($baseUrl === '' ? '' : $baseUrl) . '/' . $normalizedPath;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function e(null|string|int|float $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function auth_user(): ?array
{
    $user = Session::get('user');

    return is_array($user) ? $user : null;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($path !== '/' && str_ends_with($path, '/')) {
        return rtrim($path, '/');
    }

    return $path;
}

function is_active_path(string $path): bool
{
    return current_path() === ($path === '/' ? '/' : rtrim($path, '/'));
}