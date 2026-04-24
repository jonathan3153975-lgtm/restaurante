<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes[strtoupper($method)][$normalizedPath] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $httpMethod = strtoupper($method);
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');
        $route = $this->routes[$httpMethod][$path] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo 'Página não encontrada.';
            return;
        }

        $this->runMiddleware($route['middleware']);
        $this->invokeHandler($route['handler']);
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $entry) {
            if ($entry === 'auth' && !Session::has('user')) {
                Session::flash('error', 'Faça login para acessar o painel.');
                redirect('/login');
            }

            if ($entry === 'guest' && Session::has('user')) {
                redirect('/admin');
            }
        }
    }

    private function invokeHandler(callable|array $handler): void
    {
        if (is_callable($handler)) {
            $handler();
            return;
        }

        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->{$method}();
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        $normalized = '/' . trim($path, '/');

        return $normalized === '//' ? '/' : $normalized;
    }
}