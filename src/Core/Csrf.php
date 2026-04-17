<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf_token');

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }

        return $token;
    }

    public static function validate(?string $token): void
    {
        if ($token === null || !hash_equals((string) Session::get('_csrf_token'), $token)) {
            throw new RuntimeException('Falha de validação CSRF.');
        }
    }
}
