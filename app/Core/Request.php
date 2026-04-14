<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $params = [];

    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $queryParams,
        private readonly array $requestData,
        private readonly array $server
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return new self($method, $uri, $_GET, $_POST, $_SERVER);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        $path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        return rtrim($path, '/') ?: '/';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->requestData[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->requestData);
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function setParam(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }

    public function session(): array
    {
        return $_SESSION ?? [];
    }
}
