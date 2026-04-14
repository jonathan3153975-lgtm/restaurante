<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ModuleData;
use App\Core\Request;
use App\Core\Response;

final class AdminController extends Controller
{
    public function notasEntrada(Request $request): Response
    {
        return $this->view('admin/notas_entrada', [
            'registros' => ModuleData::adminNfs(),
        ]);
    }
}
