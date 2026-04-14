<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use DateTime;
use PDO;

final class FornecedorController extends Controller
{
    private PDO $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->pdo = Database::connection($config['db']);
    }

    public function index(Request $request): Response
    {
        $builtQuery = 'SELECT * FROM fornecedores WHERE tenant_id = :tenant_id';
        $params = ['tenant_id' => $this->getCurrentTenantId()];

        // Filtro por texto
        $filtroTexto = trim((string) $request->input('filtro', ''));
        if (!empty($filtroTexto)) {
            $builtQuery .= ' AND (nome LIKE :filtro OR cnpj LIKE :filtro)';
            $params['filtro'] = '%' . $filtroTexto . '%';
        }

        // Filtro por mês e ano
        $filtroMes = (int) $request->input('mes', 0);
        $filtroAno = (int) $request->input('ano', 0);

        if ($filtroMes > 0 && $filtroAno > 0) {
            $dataInicio = sprintf('%04d-%02d-01', $filtroAno, $filtroMes);
            $proximoMes = $filtroMes === 12 ? 1 : $filtroMes + 1;
            $proximoAno = $filtroMes === 12 ? $filtroAno + 1 : $filtroAno;
            $dataFim = sprintf('%04d-%02d-01', $proximoAno, $proximoMes);

            $builtQuery .= ' AND created_at >= :data_inicio AND created_at < :data_fim';
            $params['data_inicio'] = $dataInicio;
            $params['data_fim'] = $dataFim;
        }

        $builtQuery .= ' ORDER BY created_at DESC';

        try {
            $stmt = $this->pdo->prepare($builtQuery);
            $stmt->execute($params);
            $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $fornecedores = [];
        }

        return $this->view('admin/fornecedores/index', [
            'fornecedores' => $fornecedores,
            'csrf' => Csrf::token(),
            'filtroTexto' => $filtroTexto,
            'filtroMes' => $filtroMes,
            'filtroAno' => $filtroAno,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin/fornecedores/form', [
            'csrf' => Csrf::token(),
            'fornecedor' => null,
            'acao' => 'criar',
        ]);
    }

    public function store(Request $request): Response
    {
        $token = (string) $request->input('_csrf', '');
        if (!Csrf::validate($token)) {
            return Response::json(['sucesso' => false, 'erro' => 'Sessão expirada.'], 403);
        }

        $erros = $this->validarDados($request);
        if (!empty($erros)) {
            return Response::json(['sucesso' => false, 'erros' => $erros], 422);
        }

        $nome = trim((string) $request->input('nome', ''));
        $cnpj = preg_replace('/[^0-9]/', '', (string) $request->input('cnpj', ''));
        $contato = trim((string) $request->input('contato', ''));
        $telefone = preg_replace('/[^0-9]/', '', (string) $request->input('telefone', ''));
        $email = filter_var((string) $request->input('email', ''), FILTER_SANITIZE_EMAIL);

        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO fornecedores (tenant_id, nome, cnpj, contato, telefone, email, created_at, updated_at)
                VALUES (:tenant_id, :nome, :cnpj, :contato, :telefone, :email, NOW(), NOW())
            ');

            $stmt->execute([
                'tenant_id' => $this->getCurrentTenantId(),
                'nome' => $nome,
                'cnpj' => $cnpj ?: null,
                'contato' => $contato ?: null,
                'telefone' => $telefone ?: null,
                'email' => $email ?: null,
            ]);

            $fornecedorId = (int) $this->pdo->lastInsertId();

            return Response::json(
                ['sucesso' => true, 'mensagem' => 'Fornecedor criado com sucesso!', 'id' => $fornecedorId]
            );
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao criar fornecedor.'], 500);
        }
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->param('id', 0);

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM fornecedores WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fornecedor) {
                return Response::redirect('/admin/fornecedores');
            }

            return $this->view('admin/fornecedores/form', [
                'csrf' => Csrf::token(),
                'fornecedor' => $fornecedor,
                'acao' => 'editar',
            ]);
        } catch (\Exception $e) {
            return Response::redirect('/admin/fornecedores');
        }
    }

    public function update(Request $request): Response
    {
        $token = (string) $request->input('_csrf', '');
        if (!Csrf::validate($token)) {
            return Response::json(['sucesso' => false, 'erro' => 'Sessão expirada.'], 403);
        }

        $id = (int) $request->input('id', 0);
        if ($id <= 0) {
            return Response::json(['sucesso' => false, 'erro' => 'ID inválido.'], 400);
        }

        $erros = $this->validarDados($request);
        if (!empty($erros)) {
            return Response::json(['sucesso' => false, 'erros' => $erros], 422);
        }

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM fornecedores WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            if (!$stmt->fetch()) {
                return Response::json(['sucesso' => false, 'erro' => 'Fornecedor não encontrado.'], 404);
            }

            $nome = trim((string) $request->input('nome', ''));
            $cnpj = preg_replace('/[^0-9]/', '', (string) $request->input('cnpj', ''));
            $contato = trim((string) $request->input('contato', ''));
            $telefone = preg_replace('/[^0-9]/', '', (string) $request->input('telefone', ''));
            $email = filter_var((string) $request->input('email', ''), FILTER_SANITIZE_EMAIL);

            $stmt = $this->pdo->prepare('
                UPDATE fornecedores 
                SET nome = :nome, cnpj = :cnpj, contato = :contato, telefone = :telefone, email = :email, updated_at = NOW()
                WHERE id = :id AND tenant_id = :tenant_id
            ');

            $stmt->execute([
                'id' => $id,
                'tenant_id' => $this->getCurrentTenantId(),
                'nome' => $nome,
                'cnpj' => $cnpj ?: null,
                'contato' => $contato ?: null,
                'telefone' => $telefone ?: null,
                'email' => $email ?: null,
            ]);

            return Response::json(['sucesso' => true, 'mensagem' => 'Fornecedor atualizado com sucesso!']);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao atualizar fornecedor.'], 500);
        }
    }

    public function delete(Request $request): Response
    {
        $token = (string) $request->input('_csrf', '');
        if (!Csrf::validate($token)) {
            return Response::json(['sucesso' => false, 'erro' => 'Sessão expirada.'], 403);
        }

        $id = (int) $request->input('id', 0);
        if ($id <= 0) {
            return Response::json(['sucesso' => false, 'erro' => 'ID inválido.'], 400);
        }

        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM fornecedores 
                WHERE id = :id AND tenant_id = :tenant_id
            ');

            $stmt->execute([
                'id' => $id,
                'tenant_id' => $this->getCurrentTenantId(),
            ]);

            if ($stmt->rowCount() === 0) {
                return Response::json(['sucesso' => false, 'erro' => 'Fornecedor não encontrado.'], 404);
            }

            return Response::json(['sucesso' => true, 'mensagem' => 'Fornecedor excluído com sucesso!']);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao excluir fornecedor.'], 500);
        }
    }

    public function viewJson(Request $request): Response
    {
        $id = (int) $request->param('id', 0);

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM fornecedores WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fornecedor) {
                return Response::json(['sucesso' => false, 'erro' => 'Fornecedor não encontrado.'], 404);
            }

            return Response::json(['sucesso' => true, 'fornecedor' => $fornecedor]);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao buscar fornecedor.'], 500);
        }
    }

    private function validarDados(Request $request): array
    {
        $erros = [];

        $nome = trim((string) $request->input('nome', ''));
        if (empty($nome) || strlen($nome) < 3) {
            $erros['nome'] = 'Nome deve ter pelo menos 3 caracteres.';
        }

        $email = (string) $request->input('email', '');
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'E-mail inválido.';
        }

        $cnpj = preg_replace('/[^0-9]/', '', (string) $request->input('cnpj', ''));
        if (!empty($cnpj) && strlen($cnpj) !== 14) {
            $erros['cnpj'] = 'CNPJ deve ter 14 dígitos.';
        }

        $telefone = preg_replace('/[^0-9]/', '', (string) $request->input('telefone', ''));
        if (!empty($telefone) && strlen($telefone) < 10) {
            $erros['telefone'] = 'Telefone deve ter pelo menos 10 dígitos.';
        }

        return $erros;
    }

    private function getCurrentTenantId(): int
    {
        return 1;
    }
}
