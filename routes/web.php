<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\CashierController;
use App\Controller\ClientController;
use App\Controller\DashboardController;
use App\Controller\MenuController;
use App\Controller\OrderController;
use App\Controller\TableController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [DashboardController::class, 'home']);

    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/admin', [DashboardController::class, 'index']);

    $router->get('/admin/menu', [MenuController::class, 'index']);
    $router->get('/admin/menu/create', [MenuController::class, 'create']);
    $router->post('/admin/menu/store', [MenuController::class, 'store']);
    $router->get('/admin/menu/{id}/edit', [MenuController::class, 'edit']);
    $router->post('/admin/menu/{id}/update', [MenuController::class, 'update']);
    $router->post('/admin/menu/{id}/delete', [MenuController::class, 'delete']);
    $router->post('/admin/categories/quick-store', [MenuController::class, 'quickStoreCategory']);
    $router->get('/admin/menu/preview', [MenuController::class, 'preview']);

    $router->get('/admin/tables', [TableController::class, 'index']);
    $router->get('/admin/tables/create', [TableController::class, 'create']);
    $router->post('/admin/tables/store', [TableController::class, 'store']);
    $router->post('/admin/tables/{id}/occupy', [TableController::class, 'occupy']);
    $router->post('/admin/tables/{id}/close', [TableController::class, 'close']);
    $router->post('/admin/tables/{id}/toggle', [TableController::class, 'toggle']);
    $router->post('/admin/tables/{id}/delete', [TableController::class, 'delete']);

    $router->get('/admin/orders', [OrderController::class, 'index']);
    $router->post('/admin/orders/create', [OrderController::class, 'store']);
    $router->post('/admin/orders/{id}/delivered', [OrderController::class, 'markDelivered']);

    $router->get('/admin/cashier', [CashierController::class, 'index']);
    $router->post('/admin/cashier/{sessionId}/checkout', [CashierController::class, 'checkout']);

    $router->get('/mesa/{token}', [ClientController::class, 'show']);
    $router->post('/mesa/{token}/register', [ClientController::class, 'register']);
    $router->post('/mesa/{token}/order', [ClientController::class, 'placeOrder']);
    $router->post('/mesa/{token}/payment', [ClientController::class, 'requestPayment']);
};
