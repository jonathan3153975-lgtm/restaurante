<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionMethod;
use Throwable;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = rtrim($request->uri(), '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $route['path']);
            $regex = '#^' . rtrim((string) $pattern, '/') . '$#';

            if (!preg_match($regex, $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, static fn (string|int $key): bool => is_string($key), ARRAY_FILTER_USE_KEY);
            $boundRequest = Request::capture($params);

            try {
                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $reflection = new ReflectionMethod($controller, $action);

                    if ($reflection->getNumberOfParameters() === 0) {
                        $controller->{$action}();
                    } else {
                        $controller->{$action}($boundRequest);
                    }

                    return;
                }

                $handler($boundRequest);

                return;
            } catch (Throwable $throwable) {
                http_response_code(500);
                View::render('partials/error', ['exception' => $throwable], 'layouts/guest');

                return;
            }
        }

        http_response_code(404);
        View::render('partials/not-found', [], 'layouts/guest');
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $normalizedPath = rtrim($path, '/') ?: '/';

        $this->routes[$method][] = [
            'path' => $normalizedPath,
            'handler' => $handler,
        ];
    }
}
