<?php

declare(strict_types=1);

use App\Core\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

$bootstrap = require dirname(__DIR__) . '/app/bootstrap.php';
$router = $bootstrap['router'];

$tests = [];

$addTest = static function (string $name, callable $fn) use (&$tests): void {
    $tests[] = ['name' => $name, 'fn' => $fn];
};

$assertTrue = static function (bool $condition, string $message = 'Falha de assertiva'): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$dispatch = static function (string $method, string $path) use ($router): \App\Core\Response {
    $request = new Request($method, $path, [], [], []);
    return $router->dispatch($request);
};

$moduleRoutes = [
    '/dashboard',
    '/admin/notas-entrada',
    '/estoque',
    '/estoque/saida-materiais',
    '/rh',
    '/financeiro',
    '/financeiro/caixa',
    '/cardapio',
    '/mesas',
    '/cozinha',
    '/fiscal',
    '/relatorios',
    '/mesas/cardapio-digital',
];

$addTest('Rota login carregando', function () use ($dispatch, $assertTrue): void {
    $response = $dispatch('GET', '/login');
    $assertTrue($response->status === 200, 'Login deveria retornar 200');
    $assertTrue(str_contains($response->content, 'Acesso ao Sistema'), 'Login sem conteudo esperado');
});

$addTest('Rota inexistente retorna 404', function () use ($dispatch, $assertTrue): void {
    $response = $dispatch('GET', '/rota-inexistente');
    $assertTrue($response->status === 404, 'Rota invalida deveria retornar 404');
});

$addTest('Redireciona sem autenticacao', function () use ($dispatch, $assertTrue): void {
    unset($_SESSION['user']);
    $response = $dispatch('GET', '/dashboard');
    $assertTrue($response->status === 302, 'Dashboard anonimo deveria redirecionar');
    $assertTrue(($response->headers['Location'] ?? '') === '/login', 'Redirecionamento deveria ir para /login');
});

$addTest('Todas as rotas de modulo existem', function () use ($router, $moduleRoutes, $assertTrue): void {
    foreach ($moduleRoutes as $route) {
        $assertTrue($router->hasRoute('GET', $route), 'Rota ausente: ' . $route);
    }
});

$addTest('Acesso autenticado a cada modulo', function () use ($dispatch, $moduleRoutes, $assertTrue): void {
    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Administrador',
        'email' => 'admin@restaurante.local',
        'role' => 'administrador',
        'tenant_id' => 1,
    ];

    foreach ($moduleRoutes as $route) {
        $response = $dispatch('GET', $route);
        $assertTrue($response->status === 200, 'Status invalido em ' . $route);
        $assertTrue(str_contains($response->content, '<html'), 'Conteudo invalido em ' . $route);
    }
});

$addTest('Direcionamentos do dashboard', function () use ($dispatch, $assertTrue): void {
    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Administrador',
        'email' => 'admin@restaurante.local',
        'role' => 'administrador',
        'tenant_id' => 1,
    ];

    $response = $dispatch('GET', '/dashboard');

    $targets = [
        '/admin/notas-entrada',
        '/estoque',
        '/financeiro',
        '/mesas',
    ];

    foreach ($targets as $target) {
        $assertTrue(str_contains($response->content, 'href="' . $target . '"'), 'Link nao encontrado no dashboard: ' . $target);
    }
});

$addTest('Filtro dinamico e mascaras carregados no layout', function () use ($dispatch, $assertTrue): void {
    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Administrador',
        'email' => 'admin@restaurante.local',
        'role' => 'administrador',
        'tenant_id' => 1,
    ];

    $response = $dispatch('GET', '/rh');
    $assertTrue(str_contains($response->content, 'data-mask="cpf"'), 'Campo de mascara CPF nao encontrado');
    $assertTrue(str_contains($response->content, 'data-mask="telefone"'), 'Campo de mascara telefone nao encontrado');

    $responseEstoque = $dispatch('GET', '/estoque');
    $assertTrue(str_contains($responseEstoque->content, 'data-filter-input'), 'Filtro dinamico nao encontrado');
});

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    try {
        $test['fn']();
        $passed++;
        echo '[OK] ' . $test['name'] . PHP_EOL;
    } catch (Throwable $e) {
        $failed++;
        echo '[ERRO] ' . $test['name'] . ': ' . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL;
echo 'Total: ' . count($tests) . ' | OK: ' . $passed . ' | ERRO: ' . $failed . PHP_EOL;

exit($failed > 0 ? 1 : 0);
