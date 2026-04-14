<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ModuleData;
use App\Core\Request;
use App\Core\Response;

final class EstoqueController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('estoque/index', [
            'registros' => ModuleData::estoqueProdutos(),
        ]);
    }

    public function saidaMateriais(Request $request): Response
    {
        return $this->view('estoque/saida_materiais', [
            'registros' => [
                ['data' => '2026-04-10', 'motivo' => 'Consumo interno', 'responsavel' => 'Ana Souza', 'qtd' => '2 kg'],
                ['data' => '2026-04-11', 'motivo' => 'Perda', 'responsavel' => 'Marcos Silva', 'qtd' => '1 kg'],
            ],
        ]);
    }
}
