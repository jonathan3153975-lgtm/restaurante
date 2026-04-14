<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ModuleData;
use App\Core\Request;
use App\Core\Response;

final class FinanceiroController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('financeiro/index', [
            'registros' => ModuleData::financeiroMovimentos(),
        ]);
    }

    public function caixa(Request $request): Response
    {
        return $this->view('financeiro/caixa', [
            'caixas' => [
                ['usuario' => 'Ana Souza', 'abertura' => '08:00', 'status' => 'Aberto', 'saldo' => 820.00],
                ['usuario' => 'Carlos Lima', 'abertura' => '14:00', 'status' => 'Fechado', 'saldo' => 1260.00],
            ],
        ]);
    }
}
