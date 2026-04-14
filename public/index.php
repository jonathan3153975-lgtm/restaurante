<?php

declare(strict_types=1);

use App\Core\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

$bootstrap = require dirname(__DIR__) . '/app/bootstrap.php';
$router = $bootstrap['router'];

$request = Request::capture();
$response = $router->dispatch($request);
$response->send();
