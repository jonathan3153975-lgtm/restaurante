<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

final class AuthController extends Controller
{
    public function loginForm(Request $request): Response
    {
        return $this->view('auth/login', [
            'csrf' => Csrf::token(),
            'error' => null,
        ]);
    }

    public function login(Request $request): Response
    {
        $token = (string) $request->input('_csrf', '');
        if (!Csrf::validate($token)) {
            return $this->view('auth/login', [
                'csrf' => Csrf::token(),
                'error' => 'Sessão expirada. Recarregue e tente novamente.',
            ]);
        }

        $email = filter_var((string) $request->input('email', ''), FILTER_SANITIZE_EMAIL);
        $password = (string) $request->input('password', '');

        if (!Auth::attempt($email, $password)) {
            return $this->view('auth/login', [
                'csrf' => Csrf::token(),
                'error' => 'Credenciais inválidas.',
            ]);
        }

        return $this->redirect('/dashboard');
    }

    public function logout(Request $request): Response
    {
        Auth::logout();
        return $this->redirect('/login');
    }
}
