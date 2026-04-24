<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Services\FakeUserProvider;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $provider = new FakeUserProvider();

        $this->render('auth/login', [
            'demoUsers' => $provider->demoUsers(),
            'oldEmail' => (string) Session::consume('old_email', ''),
        ], 'auth');
    }

    public function login(): void
    {
        if (!Session::validateCsrf($_POST['_token'] ?? null)) {
            Session::flash('error', 'Sessão expirada. Atualize a página e tente novamente.');
            $this->redirect('/login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        Session::flash('old_email', $email);

        $provider = new FakeUserProvider();
        $user = $provider->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Credenciais inválidas. Use um dos usuários demonstrativos.');
            $this->redirect('/login');
        }

        unset($user['password_hash']);

        Session::put('user', $user);
        Session::flash('success', 'Login simulado realizado com sucesso.');

        $this->redirect('/admin');
    }

    public function logout(): void
    {
        if (!Session::validateCsrf($_POST['_token'] ?? null)) {
            Session::flash('error', 'Não foi possível encerrar a sessão.');
            $this->redirect('/admin');
        }

        Session::destroy();
        Session::start((string) config('session_name', 'tech_food_session'));
        Session::flash('success', 'Sessão encerrada.');

        $this->redirect('/login');
    }
}