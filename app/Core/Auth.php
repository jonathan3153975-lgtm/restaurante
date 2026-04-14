<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        // Demonstra fluxo com hash e papeis; substituir por consulta real no banco.
        $users = [
            'admin@restaurante.local' => [
                'id' => 1,
                'name' => 'Administrador',
                'role' => 'administrador',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
            ],
            'gerente@restaurante.local' => [
                'id' => 2,
                'name' => 'Gerente',
                'role' => 'gerente',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
            ],
        ];

        $user = $users[$email] ?? null;

        if ($user === null || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $email,
            'role' => $user['role'],
            'tenant_id' => 1,
        ];

        return true;
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        return is_array($user) ? $user : null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }
}
