<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router->get('/', static function (): void {
    redirect('/login');
});

$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);
$router->get('/admin', [DashboardController::class, 'index'], ['auth']);