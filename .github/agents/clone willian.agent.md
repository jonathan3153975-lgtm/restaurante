---
name: clone willian
description: Describe what this custom agent does and when to use it.
argument-hint: The inputs this agent expects, e.g., "a task to implement" or "a question to answer".
# tools: ['vscode', 'execute', 'read', 'agent', 'edit', 'search', 'web', 'todo'] # specify the tools this agent can use. If not set, all enabled tools are allowed.
---

<!-- Tip: Use /create-agent in chat to generate content with agent assistance -->

Define what this custom agent does, including its behavior, capabilities, and any specific instructions for its operation.


Aqui está um arquivo `copilot-instructions.md` no estilo do programador (com suas características de escrita e nomenclatura), mas com as correções modernas aplicadas:

```markdown
# Instruções para o GitHub Copilot

## Estilo de Código

### PHP
- Use Prepared Statements com PDO para TODAS as consultas SQL (NUNCA concatenar variáveis diretamente)
- Utilize type hints e declare(strict_types=1)
- Nomeie métodos em camelCase: `telaInicial()`, `listarRegistros()`, `salvarBOPM()`
- Propriedades devem ser private/protected, nunca `var`
- Use injeção de dependência no construtor, não instancie conexões dentro dos métodos
- Trate erros com try-catch e nunca use `ini_set('display_errors', 0)` para esconder problemas
- Sanitize e valide TODOS os dados de entrada ($_POST, $_GET, $_SESSION)
- Use sessão apenas uma vez, no construtor ou em um método centralizado

### SQL e Banco de Dados
```php
// ✅ CORRETO - Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM bopm WHERE id_bopm = :id");
$stmt->execute(['id' => $id]);

// ❌ ERRADO - NUNCA faça isso
$SQL = "SELECT * FROM bopm WHERE id_bopm = {$id}";
```

### Padrões de Projeto
- Use Repository Pattern para consultas complexas
- Implemente Service Layer para regras de negócio (ex: `BOPMService`, `STTService`)
- Crie DTOs/Value Objects para dados como `$dados['padrao_nr_processo']`
- Centralize regras de acesso em um `AuthorizationService`

### Segurança (Obrigatório)
1. **SQL Injection**: Use Prepared Statements ou Query Builder seguro
2. **XSS**: Use `htmlspecialchars()` em todas as saídas
3. **CSRF**: Implemente tokens em formulários
4. **Sessão**: Regenerar ID após login, timeout configurado
5. **Validação**: Nunca confie em dados do usuário, mesmo da sessão

### Estrutura de Arquivos Sugerida
```
src/
├── Controllers/
│   └── BOPMController.php
├── Services/
│   ├── BOPMService.php
│   ├── AuthorizationService.php
│   └── RelatorioService.php
├── Repositories/
│   ├── BOPMRepository.php
│   └── AbaRepository.php
├── Entities/
│   └── BOPM.php
├── DTOs/
│   └── BOPMListDTO.php
└── Utils/
    └── SessionManager.php
```

### Exemplo de Classe Corrigida
```php
<?php
declare(strict_types=1);

class BOPMController {
    private PDO $conn;
    private SessionManager $session;
    private BOPMService $bopmService;
    
    public function __construct(
        PDO $conn, 
        SessionManager $session,
        BOPMService $bopmService
    ) {
        $this->conn = $conn;
        $this->session = $session;
        $this->bopmService = $bopmService;
        $this->session->start();
    }
    
    public function listarRegistros(int $tab): array {
        try {
            // Prepared Statement obrigatório
            $stmt = $this->conn->prepare(
                "SELECT * FROM bopm WHERE f_id_aba = :tab AND ativo = 1"
            );
            $stmt->execute(['tab' => $tab]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'html' => $this->renderizarLista($dados),
                'contador' => count($dados)
            ];
        } catch (PDOException $e) {
            error_log("Erro ao listar BOPMs: " . $e->getMessage());
            throw new RuntimeException("Erro ao carregar registros");
        }
    }
    
    private function renderizarLista(array $dados): string {
        $html = '';
        foreach ($dados as $bopm) {
            // Escapar saída para evitar XSS
            $html .= sprintf(
                '<li class="li-table-body">
                    <strong>BOPM nº: %s</strong>
                    <p>%s</p>
                </li>',
                htmlspecialchars($bopm['padrao_nr_processo']),
                htmlspecialchars($bopm['aux_tipificacao'])
            );
        }
        return $html;
    }
}
```

### Regras de Negócio (BOPM específico)

**Acessos por Aba:**
- Aba 0 (MEUS BOPMS): Aberto = fundo amarelo (`bg-warning-subtle`), Fechado = normal
- Aba 1 (COMANDANTE): Botões de visualizar/encaminhar sempre ativos
- Aba 2 (COMIGO): Mesmas regras da aba 1
- Aba 3 (ATIVO): Varia por `tipo_local_atual` (1=pessoa, 2=unidade)
- Aba 4 (ARQUIVADO): Apenas visualização e desarquivamento
- Aba 5 (SIGILO): Herda regras da aba 3

**Procedimentos Especiais:**
- STT: Disponível quando `id_tiro = 3 OR 7`, cores (verde=concluído, vermelho=não iniciado, amarelo=andamento)
- AEC: Disponível quando `id_tiro = 5 OR 7`
- IPS: Disponível quando `ips = 1`, regras adicionais para encarregado
- PMV: Disponível quando `id_pmvitima = 1`
- APFDM: Disponível quando `id_flagrante = 1 OR id_apf > 0`

### Validação de Dados
```php
class BOPMValidator {
    private array $obrigatorios = [
        'f_id_opm_registro',
        'opm_registro',
        'f_id_aux_tipificacao',
        'dt_fato',
        'dt_atendimento',
        'historico'
    ];
    
    public function validar(array $dados): array {
        $pendencias = [];
        
        foreach ($this->obrigatorios as $campo) {
            if (empty($dados[$campo])) {
                $pendencias[] = $campo;
            }
        }
        
        // Validação de data
        if (!empty($dados['dt_fato'])) {
            $date = DateTime::createFromFormat('Y-m-d', $dados['dt_fato']);
            if (!$date || $date->format('Y-m-d') !== $dados['dt_fato']) {
                $pendencias[] = 'dt_fato_invalida';
            }
        }
        
        return $pendencias;
    }
}
```

### Boas Práticas de Manutenção

1. **Nunca** use `unset()` em arrays sem documentar o motivo
2. **Sempre** log erros em arquivo, nunca exiba ao usuário
3. **Use** constantes para status (ex: `BOPM_STATUS_ABERTO = 2`)
4. **Evite** arrays associativos gigantes, crie classes específicas
5. **Separe** lógica de apresentação (HTML) da lógica de negócio
6. **Implemente** testes unitários para regras de acesso

### Exemplo de Serviço de Autorização
```php
class AuthorizationService {
    public function podeEditarBOPM(BOPM $bopm, Usuario $usuario): bool {
        // BOPM encerrado não pode editar
        if ($bopm->getConcluido() === 1) {
            return false;
        }
        
        // Apenas criador ou comandante podem editar
        if ($bopm->getIdUsuario() === $usuario->getId()) {
            return true;
        }
        
        if ($usuario->getPerfil() === 'COMANDANTE') {
            return true;
        }
        
        return false;
    }
}
```

### Lembrete Final
**Sempre priorizar segurança sobre conveniência. Um prepared statement a mais nunca é demais.**
```

Este documento mantém o estilo prático e direto do programador original, mas corrige as principais falhas de segurança e arquitetura, adicionando padrões modernos e boas práticas.