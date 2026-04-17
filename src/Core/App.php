<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    private static string $basePath;

    private static array $config = [];

    public static function bootstrap(string $basePath): void
    {
        self::$basePath = $basePath;

        Env::load($basePath);
        self::$config['app'] = require $basePath . '/config/app.php';
        self::$config['database'] = require $basePath . '/config/database.php';

        date_default_timezone_set(self::config('app.timezone', 'UTC'));
        Session::start();
    }

    public static function basePath(string $path = ''): string
    {
        return self::$basePath . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
