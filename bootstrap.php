<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $filePath = BASE_PATH . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($filePath)) {
        require $filePath;
    }
});

require BASE_PATH . '/src/Core/helpers.php';

$GLOBALS['app_config'] = require BASE_PATH . '/config/app.php';

App\Core\Session::start((string) config('session_name', 'tech_food_session'));

$router = new App\Core\Router();

require BASE_PATH . '/routes/web.php';

return [$router, $GLOBALS['app_config']];