<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Service\AuthService;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly ?AuthService $authService = null
    ) {}

    public function showLogin(): void
    {
        if (current_user() !== null) {
            $this->redirect('/admin');
        }

        $this->render('auth/login', [], 'layouts/guest');
    }

    public function login(Request $request): void
    {
        $this->validateCsrf($request);

        $email = filter_var((string) $request->input('email'), FILTER_SANITIZE_EMAIL);
        $password = (string) $request->input('password');

        if ($email === '' || $password === '') {
            Session::setOld($request->all());
            Session::flash('error', 'Informe e-mail e senha.');
            $this->redirect('/login');
        }

        $authService = $this->authService ?? new AuthService();

        if (!$authService->attempt($email, $password)) {
            Session::setOld($request->all());
            Session::flash('error', 'Credenciais inválidas.');
            $this->redirect('/login');
        }

        Session::flash('success', 'Acesso realizado com sucesso.');
        $this->redirect('/admin');
    }

    public function logout(Request $request): void
    {
        $this->validateCsrf($request);
        Session::destroy();
        $this->redirect('/login');
    }
}
