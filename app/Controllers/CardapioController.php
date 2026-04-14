<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ModuleData;
use App\Core\Request;
use App\Core\Response;

final class CardapioController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('cardapio/index', [
            'registros' => ModuleData::cardapioItens(),
        ]);
    }
}
