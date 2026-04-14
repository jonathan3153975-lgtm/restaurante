<?php
$config = require __DIR__ . '/app/Config/config.php';
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $config['db']['host'],
    (int) $config['db']['port'],
    $config['db']['database']
);

try {
    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    
    // Tentar adicionar a coluna
    try {
        $pdo->exec('ALTER TABLE fornecedores ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at');
        echo "✓ Coluna updated_at adicionada com sucesso.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "✓ Coluna updated_at já existe.\n";
        } else {
            throw $e;
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
