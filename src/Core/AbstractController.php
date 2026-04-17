<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

abstract class AbstractController
{
    protected function render(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        Response::redirect($path);
    }

    protected function validateCsrf(Request $request): void
    {
        Csrf::validate((string) $request->input('_token'));
    }

    protected function requireAuth(array $roles = []): array
    {
        $user = Session::get('auth_user');

        if (!is_array($user)) {
            Session::flash('error', 'Faça login para continuar.');
            $this->redirect('/login');
        }

        if ($roles !== [] && !in_array($user['role'], $roles, true)) {
            throw new RuntimeException('Você não tem permissão para acessar este recurso.');
        }

        return $user;
    }
}
