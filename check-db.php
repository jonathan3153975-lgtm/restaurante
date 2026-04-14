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
    $result = $pdo->query('DESCRIBE fornecedores');
    
    if ($result === false) {
        echo "Tabela fornecedores não existe ou erro ao descrever.\n";
        exit(1);
    }
    
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "Colunas da tabela fornecedores:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")" . ($col['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . "\n";
}
