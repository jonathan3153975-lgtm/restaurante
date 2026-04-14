<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/app/Config/config.php';

// Criar banco de dados
$dsn = sprintf(
    'mysql:host=%s;port=%d;charset=utf8mb4',
    $config['db']['host'],
    (int) $config['db']['port']
);

try {
    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    
    // Criar database
    $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . $config['db']['database'] . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci');
    
    echo "✓ Banco de dados criado/verificado.\n";
    
    // Conectar ao database
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $config['db']['host'],
        (int) $config['db']['port'],
        $config['db']['database']
    );
    
    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    
    // Executar schema
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    // Remover comentários
    $schema = preg_replace('/--.*$/m', '', $schema);
    $schema = preg_replace('/\/\*.*?\*\//s', '', $schema);
    
    // Split por `;` que não está dentro de aspas
    $statements = [];
    $current = '';
    $inString = false;
    $stringChar = '';
    
    for ($i = 0; $i < strlen($schema); $i++) {
        $char = $schema[$i];
        
        if (($char === '"' || $char === "'") && ($i === 0 || $schema[$i - 1] !== '\\')) {
            if (!$inString) {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === $stringChar) {
                $inString = false;
            }
        }
        
        if ($char === ';' && !$inString) {
            $statements[] = trim($current);
            $current = '';
        } else {
            $current .= $char;
        }
    }
    
    if (trim($current)) {
        $statements[] = trim($current);
    }
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignorar erros de tabela já existente
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Erro ao executar:\n" . substr($statement, 0, 100) . "...\n";
                    echo "Mensagem: " . $e->getMessage() . "\n\n";
                }
            }
        }
    }
    
    echo "✓ Schema de banco de dados criado/verificado.\n";
    
    // Inserir dados de exemplo
    $pdo->exec("
        INSERT INTO tenants (id, nome_fantasia, razao_social, cnpj, email, status)
        VALUES (1, 'Restaurante Exemplo', 'Restaurante Exemplo LTDA', '12345678000190', 'contato@restaurante.local', 'ativo')
        ON DUPLICATE KEY UPDATE nome_fantasia = nome_fantasia
    ");
    
    echo "✓ Tenant de exemplo criado/verificado.\n";
    
    // Inserir usuário admin
    $senhaHash = password_hash('123456', PASSWORD_BCRYPT);
    
    $pdo->exec("
        INSERT INTO users (tenant_id, nome, email, senha_hash, perfil, ativo)
        VALUES (1, 'Administrador', 'admin@restaurante.local', '$senhaHash', 'administrador', 1)
        ON DUPLICATE KEY UPDATE nome = nome
    ");
    
    echo "✓ Usuário admin criado/verificado.\n";
    
    // Inserir fornecedores de exemplo
    $fornecedores = [
        ['nome' => 'Fornecedor A', 'cnpj' => '11222333000181', 'contato' => 'João Silva', 'telefone' => '11999999999', 'email' => 'fornecedor.a@exemplo.com'],
        ['nome' => 'Fornecedor B', 'cnpj' => '22333444000182', 'contato' => 'Maria Santos', 'telefone' => '11888888888', 'email' => 'fornecedor.b@exemplo.com'],
        ['nome' => 'Fornecedor C', 'cnpj' => '33444555000183', 'contato' => 'Pedro Oliveira', 'telefone' => '11777777777', 'email' => 'fornecedor.c@exemplo.com'],
    ];
    
    $stmt = $pdo->prepare('
        INSERT INTO fornecedores (tenant_id, nome, cnpj, contato, telefone, email, created_at, updated_at)
        VALUES (:tenant_id, :nome, :cnpj, :contato, :telefone, :email, NOW(), NOW())
        ON DUPLICATE KEY UPDATE nome = nome
    ');
    
    foreach ($fornecedores as $f) {
        $stmt->execute([
            'tenant_id' => 1,
            'nome' => $f['nome'],
            'cnpj' => $f['cnpj'],
            'contato' => $f['contato'],
            'telefone' => $f['telefone'],
            'email' => $f['email'],
        ]);
    }
    
    echo "✓ Fornecedores de exemplo criados.\n\n";
    echo "✅ Banco de dados inicializado com sucesso!\n";
    echo "📧 Email: admin@restaurante.local\n";
    echo "🔐 Senha: 123456\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
