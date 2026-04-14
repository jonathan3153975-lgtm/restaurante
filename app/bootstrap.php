<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CardapioController;
use App\Controllers\CozinhaController;
use App\Controllers\DashboardController;
use App\Controllers\EstoqueController;
use App\Controllers\FinanceiroController;
use App\Controllers\FiscalController;
use App\Controllers\FornecedorController;
use App\Controllers\MesasController;
use App\Controllers\NotasEntradaController;
use App\Controllers\RelatoriosController;
use App\Controllers\RhController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require __DIR__ . '/Config/config.php';

$router = new Router();

$authController = new AuthController();
$dashboardController = new DashboardController();
$adminController = new AdminController();
$estoqueController = new EstoqueController();
$rhController = new RhController();
$financeiroController = new FinanceiroController();
$cardapioController = new CardapioController();
$mesasController = new MesasController();
$cozinhaController = new CozinhaController();
$fiscalController = new FiscalController();
$relatoriosController = new RelatoriosController();
$fornecedorController = new FornecedorController();
$notasEntradaController = new NotasEntradaController();

$router->get('/', static fn (Request $request): Response => Response::redirect('/dashboard'));
$router->get('/login', fn (Request $request): Response => $authController->loginForm($request));
$router->post('/login', fn (Request $request): Response => $authController->login($request));
$router->post('/logout', fn (Request $request): Response => $authController->logout($request));

$requireAuth = static function (callable $handler): callable {
    return static function (Request $request) use ($handler): Response {
        if (!Auth::check()) {
            return Response::redirect('/login');
        }

        return $handler($request);
    };
};

$router->get('/dashboard', $requireAuth(fn (Request $request): Response => $dashboardController->index($request)));
$router->get('/admin/notas-entrada', $requireAuth(fn (Request $request): Response => $notasEntradaController->index($request)));
$router->get('/admin/notas-entrada/create', $requireAuth(fn (Request $request): Response => $notasEntradaController->create($request)));
$router->post('/admin/notas-entrada/store', $requireAuth(fn (Request $request): Response => $notasEntradaController->store($request)));
$router->get('/admin/notas-entrada/:id/edit', $requireAuth(fn (Request $request): Response => $notasEntradaController->edit($request)));
$router->post('/admin/notas-entrada/update', $requireAuth(fn (Request $request): Response => $notasEntradaController->update($request)));
$router->get('/admin/notas-entrada/:id/view', $requireAuth(fn (Request $request): Response => $notasEntradaController->viewJson($request)));
$router->post('/admin/notas-entrada/delete', $requireAuth(fn (Request $request): Response => $notasEntradaController->delete($request)));
$router->get('/admin/fornecedores', $requireAuth(fn (Request $request): Response => $fornecedorController->index($request)));
$router->get('/admin/fornecedores/create', $requireAuth(fn (Request $request): Response => $fornecedorController->create($request)));
$router->post('/admin/fornecedores/store', $requireAuth(fn (Request $request): Response => $fornecedorController->store($request)));
$router->get('/admin/fornecedores/:id/edit', $requireAuth(fn (Request $request): Response => $fornecedorController->edit($request)));
$router->post('/admin/fornecedores/update', $requireAuth(fn (Request $request): Response => $fornecedorController->update($request)));
$router->get('/admin/fornecedores/:id/view', $requireAuth(fn (Request $request): Response => $fornecedorController->viewJson($request)));
$router->post('/admin/fornecedores/delete', $requireAuth(fn (Request $request): Response => $fornecedorController->delete($request)));
$router->get('/estoque', $requireAuth(fn (Request $request): Response => $estoqueController->index($request)));
$router->get('/estoque/saida-materiais', $requireAuth(fn (Request $request): Response => $estoqueController->saidaMateriais($request)));
$router->get('/rh', $requireAuth(fn (Request $request): Response => $rhController->index($request)));
$router->get('/financeiro', $requireAuth(fn (Request $request): Response => $financeiroController->index($request)));
$router->get('/financeiro/caixa', $requireAuth(fn (Request $request): Response => $financeiroController->caixa($request)));
$router->get('/cardapio', $requireAuth(fn (Request $request): Response => $cardapioController->index($request)));
$router->get('/mesas', $requireAuth(fn (Request $request): Response => $mesasController->index($request)));
$router->get('/mesas/cardapio-digital', fn (Request $request): Response => $mesasController->cardapioDigital($request));
$router->get('/cozinha', $requireAuth(fn (Request $request): Response => $cozinhaController->index($request)));
$router->get('/fiscal', $requireAuth(fn (Request $request): Response => $fiscalController->index($request)));
$router->get('/relatorios', $requireAuth(fn (Request $request): Response => $relatoriosController->index($request)));

return [
    'config' => $config,
    'router' => $router,
];
