<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class FiscalController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('fiscal/index', [
            'documentos' => [
                ['numero' => 'NFCe-1234', 'status' => 'Autorizada', 'tipo' => 'NFC-e'],
                ['numero' => 'NFe-8891', 'status' => 'Contingencia', 'tipo' => 'NF-e'],
            ],
        ]);
    }
}
