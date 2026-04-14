<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class RelatoriosController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('relatorios/index', [
            'relatorios' => [
                'Vendas',
                'Produtos mais vendidos',
                'Estoque',
                'Financeiro',
                'Funcionarios',
            ],
        ]);
    }
}
