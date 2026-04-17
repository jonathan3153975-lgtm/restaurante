<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

$envPath = $basePath . '/.env';
if (is_file($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $trimmed, 2));
        $_ENV[$key] = trim($value, "\"'");
    }
}

$config = [
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_NAME'] ?? 'tech_food',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
];

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']);
$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(190) NOT NULL UNIQUE, executed_at DATETIME NOT NULL)');

$executed = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$files = glob($basePath . '/database/migrations/*.sql') ?: [];
sort($files);

foreach ($files as $file) {
    $migration = basename($file);

    if (in_array($migration, $executed, true)) {
        echo "Ignorado: {$migration}" . PHP_EOL;
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Não foi possível ler a migration ' . $migration);
    }

    $pdo->beginTransaction();
    $pdo->exec($sql);
    $statement = $pdo->prepare('INSERT INTO migrations (migration, executed_at) VALUES (:migration, NOW())');
    $statement->execute(['migration' => $migration]);
    $pdo->commit();

    echo "Executada: {$migration}" . PHP_EOL;
}

echo 'Migrações concluídas.' . PHP_EOL;
