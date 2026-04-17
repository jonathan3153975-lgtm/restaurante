<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Session;
use App\Repository\AuthRepository;

final class AuthService
{
    public function __construct(
        private readonly AuthRepository $repository = new AuthRepository()
    ) {}

    public function attempt(string $email, string $password): bool
    {
        $user = $this->repository->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            return false;
        }

        unset($user['password']);
        session_regenerate_id(true);
        Session::set('auth_user', $user);

        return true;
    }
}
