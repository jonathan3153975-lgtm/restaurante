<?php

declare(strict_types=1);

namespace App\Services;

final class FakeUserProvider
{
    private array $users;

    public function __construct()
    {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $this->users = [
            [
                'id' => 1,
                'name' => 'Amanda Ribeiro',
                'email' => 'admin@techfood.local',
                'role' => 'Administrador',
                'tenant' => 'Bistrô Lumiere',
                'avatar' => 'AR',
                'password_hash' => $defaultPassword,
            ],
            [
                'id' => 2,
                'name' => 'Guilherme Costa',
                'email' => 'gerente@techfood.local',
                'role' => 'Gerente',
                'tenant' => 'Bistrô Lumiere',
                'avatar' => 'GC',
                'password_hash' => $defaultPassword,
            ],
            [
                'id' => 3,
                'name' => 'Patricia Nunes',
                'email' => 'caixa@techfood.local',
                'role' => 'Caixa',
                'tenant' => 'Bistrô Lumiere',
                'avatar' => 'PN',
                'password_hash' => $defaultPassword,
            ],
        ];
    }

    public function findByEmail(string $email): ?array
    {
        foreach ($this->users as $user) {
            if (strcasecmp($user['email'], trim($email)) === 0) {
                return $user;
            }
        }

        return null;
    }

    public function demoUsers(): array
    {
        return array_map(static function (array $user): array {
            unset($user['password_hash']);

            return $user;
        }, $this->users);
    }
}