<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('app_config')) {
    function app_config(string $key, mixed $default = null): mixed
    {
        return App\Core\App::config($key, $default);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::consumeOld($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(?string $type = null): mixed
    {
        $messages = Session::consumeFlash();

        if ($type === null) {
            return $messages;
        }

        return $messages[$type] ?? null;
    }
}

if (!function_exists('money')) {
    function money(float $amount): string
    {
        return 'R$ ' . number_format($amount, 2, ',', '.');
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        return Session::get('auth_user');
    }
}

if (!function_exists('has_role')) {
    function has_role(string ...$roles): bool
    {
        $user = current_user();

        if ($user === null) {
            return false;
        }

        return in_array($user['role'], $roles, true);
    }
}
