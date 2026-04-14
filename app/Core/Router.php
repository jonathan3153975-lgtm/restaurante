<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<string|int, callable|array>> */
    private array $routes = [];
    private array $dynamicRoutes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $normalizedPath = rtrim($path, '/') ?: '/';
        
        // Verifica se a rota tem parâmetros dinâmicos
        if (preg_match('/:[a-zA-Z_][a-zA-Z0-9_]*/', $normalizedPath)) {
            $pattern = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_]*)/', '(?P<$1>[^/]+)', $normalizedPath);
            $pattern = '#^' . $pattern . '$#';
            $this->dynamicRoutes[strtoupper($method)][] = [
                'pattern' => $pattern,
                'handler' => $handler,
                'paramNames' => $this->extractParamNames($normalizedPath),
            ];
        } else {
            $this->routes[strtoupper($method)][$normalizedPath] = $handler;
        }
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $path, $matches);
        return $matches[1] ?? [];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        // Tenta rotas estáticas primeiro
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            // Tenta rotas dinâmicas
            [$handler, $params] = $this->findDynamicRoute($method, $path) ?? [null, []];
            
            if ($handler !== null) {
                foreach ($params as $key => $value) {
                    $request->setParam($key, $value);
                }
            }
        }

        if ($handler === null) {
            return Response::html(View::render('errors/404', ['path' => $path]), 404);
        }

        $response = $handler($request);

        if (!$response instanceof Response) {
            throw new \RuntimeException('Handlers devem retornar instancia de Response.');
        }

        return $response;
    }

    private function findDynamicRoute(string $method, string $path): ?array
    {
        if (!isset($this->dynamicRoutes[$method])) {
            return null;
        }

        foreach ($this->dynamicRoutes[$method] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = [];
                foreach ($route['paramNames'] as $paramName) {
                    if (isset($matches[$paramName])) {
                        $params[$paramName] = $matches[$paramName];
                    }
                }
                return [$route['handler'], $params];
            }
        }

        return null;
    }

    public function hasRoute(string $method, string $path): bool
    {
        $normalizedPath = rtrim($path, '/') ?: '/';
        if (isset($this->routes[strtoupper($method)][$normalizedPath])) {
            return true;
        }

        return $this->findDynamicRoute(strtoupper($method), $normalizedPath) !== null;
    }

    public function allRoutes(): array
    {
        return $this->routes;
    }
}
