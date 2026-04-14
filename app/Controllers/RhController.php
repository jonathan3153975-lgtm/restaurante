<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\ModuleData;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\FuncionarioRepository;
use App\Services\FuncionarioService;

final class RhController extends Controller
{
    public function index(Request $request): Response
    {
        $registros = ModuleData::rhFuncionarios();

        try {
            $config = require dirname(__DIR__) . '/Config/config.php';
            $pdo = Database::connection($config['db']);
            $service = new FuncionarioService(new FuncionarioRepository($pdo));
            $tenantId = (int) ($_SESSION['user']['tenant_id'] ?? 1);
            $filtro = (string) $request->input('q', '');
            $resultado = $service->buscarFuncionarios($tenantId, $filtro);

            if (!empty($resultado)) {
                $registros = $resultado;
            }
        } catch (\Throwable $e) {
            // Fallback para dados mock quando banco ainda nao esta provisionado.
        }

        return $this->view('rh/index', [
            'registros' => $registros,
        ]);
    }
}
