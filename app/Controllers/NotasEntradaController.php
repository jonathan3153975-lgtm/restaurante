<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use PDO;

final class NotasEntradaController extends Controller
{
    private PDO $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->pdo = Database::connection($config['db']);
    }

    public function index(Request $request): Response
    {
        $builtQuery = 'SELECT ne.*, f.nome as fornecedor_nome FROM notas_entrada ne 
                       LEFT JOIN fornecedores f ON ne.fornecedor_id = f.id
                       WHERE ne.tenant_id = :tenant_id';
        $params = ['tenant_id' => $this->getCurrentTenantId()];

        // Filtro por texto (número NF ou fornecedor)
        $filtroTexto = trim((string) $request->input('filtro', ''));
        if (!empty($filtroTexto)) {
            $builtQuery .= ' AND (ne.numero_nf LIKE :filtro OR f.nome LIKE :filtro)';
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

            $builtQuery .= ' AND ne.data_emissao >= :data_inicio AND ne.data_emissao < :data_fim';
            $params['data_inicio'] = $dataInicio;
            $params['data_fim'] = $dataFim;
        }

        $builtQuery .= ' ORDER BY ne.created_at DESC';

        try {
            $stmt = $this->pdo->prepare($builtQuery);
            $stmt->execute($params);
            $notasEntrada = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $notasEntrada = [];
        }

        // Buscar fornecedores para dropdown
        try {
            $stmt = $this->pdo->prepare('SELECT id, nome FROM fornecedores WHERE tenant_id = :tenant_id ORDER BY nome');
            $stmt->execute(['tenant_id' => $this->getCurrentTenantId()]);
            $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $fornecedores = [];
        }

        return $this->view('admin/notas_entrada/index', [
            'notasEntrada' => $notasEntrada,
            'fornecedores' => $fornecedores,
            'csrf' => Csrf::token(),
            'filtroTexto' => $filtroTexto,
            'filtroMes' => $filtroMes,
            'filtroAno' => $filtroAno,
        ]);
    }

    public function create(Request $request): Response
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id, nome FROM fornecedores WHERE tenant_id = :tenant_id ORDER BY nome');
            $stmt->execute(['tenant_id' => $this->getCurrentTenantId()]);
            $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $fornecedores = [];
        }

        try {
            $stmt = $this->pdo->prepare('SELECT id, nome FROM centros_custo WHERE tenant_id = :tenant_id ORDER BY nome');
            $stmt->execute(['tenant_id' => $this->getCurrentTenantId()]);
            $centrosCusto = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $centrosCusto = [];
        }

        return $this->view('admin/notas_entrada/form', [
            'csrf' => Csrf::token(),
            'notaEntrada' => null,
            'fornecedores' => $fornecedores,
            'centrosCusto' => $centrosCusto,
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

        $numeroNf = trim((string) $request->input('numero_nf', ''));
        $serie = trim((string) $request->input('serie', ''));
        $fornecedorId = (int) $request->input('fornecedor_id', 0);
        $dataEmissao = (string) $request->input('data_emissao', '');
        $valorTotal = (float) str_replace(',', '.', (string) $request->input('valor_total', 0));
        $icms = (float) str_replace(',', '.', (string) $request->input('icms', 0));
        $ipi = (float) str_replace(',', '.', (string) $request->input('ipi', 0));
        $pis = (float) str_replace(',', '.', (string) $request->input('pis', 0));
        $cofins = (float) str_replace(',', '.', (string) $request->input('cofins', 0));
        $centroCustoId = (int) $request->input('centro_custo_id', 0) ?: null;

        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO notas_entrada 
                (tenant_id, fornecedor_id, numero_nf, serie, data_emissao, valor_total, icms, ipi, pis, cofins, centro_custo_id, created_at)
                VALUES (:tenant_id, :fornecedor_id, :numero_nf, :serie, :data_emissao, :valor_total, :icms, :ipi, :pis, :cofins, :centro_custo_id, NOW())
            ');

            $stmt->execute([
                'tenant_id' => $this->getCurrentTenantId(),
                'fornecedor_id' => $fornecedorId,
                'numero_nf' => $numeroNf,
                'serie' => $serie ?: null,
                'data_emissao' => $dataEmissao,
                'valor_total' => $valorTotal,
                'icms' => $icms,
                'ipi' => $ipi,
                'pis' => $pis,
                'cofins' => $cofins,
                'centro_custo_id' => $centroCustoId,
            ]);

            $notaId = (int) $this->pdo->lastInsertId();

            return Response::json(
                ['sucesso' => true, 'mensagem' => 'Nota fiscal criada com sucesso!', 'id' => $notaId]
            );
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao criar nota fiscal.'], 500);
        }
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->param('id', 0);

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM notas_entrada WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            $notaEntrada = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$notaEntrada) {
                return Response::redirect('/admin/notas-entrada');
            }

            $stmt = $this->pdo->prepare('SELECT id, nome FROM fornecedores WHERE tenant_id = :tenant_id ORDER BY nome');
            $stmt->execute(['tenant_id' => $this->getCurrentTenantId()]);
            $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare('SELECT id, nome FROM centros_custo WHERE tenant_id = :tenant_id ORDER BY nome');
            $stmt->execute(['tenant_id' => $this->getCurrentTenantId()]);
            $centrosCusto = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->view('admin/notas_entrada/form', [
                'csrf' => Csrf::token(),
                'notaEntrada' => $notaEntrada,
                'fornecedores' => $fornecedores,
                'centrosCusto' => $centrosCusto,
                'acao' => 'editar',
            ]);
        } catch (\Exception $e) {
            return Response::redirect('/admin/notas-entrada');
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
            $stmt = $this->pdo->prepare('SELECT * FROM notas_entrada WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            if (!$stmt->fetch()) {
                return Response::json(['sucesso' => false, 'erro' => 'Nota fiscal não encontrada.'], 404);
            }

            $numeroNf = trim((string) $request->input('numero_nf', ''));
            $serie = trim((string) $request->input('serie', ''));
            $fornecedorId = (int) $request->input('fornecedor_id', 0);
            $dataEmissao = (string) $request->input('data_emissao', '');
            $valorTotal = (float) str_replace(',', '.', (string) $request->input('valor_total', 0));
            $icms = (float) str_replace(',', '.', (string) $request->input('icms', 0));
            $ipi = (float) str_replace(',', '.', (string) $request->input('ipi', 0));
            $pis = (float) str_replace(',', '.', (string) $request->input('pis', 0));
            $cofins = (float) str_replace(',', '.', (string) $request->input('cofins', 0));
            $centroCustoId = (int) $request->input('centro_custo_id', 0) ?: null;

            $stmt = $this->pdo->prepare('
                UPDATE notas_entrada 
                SET numero_nf = :numero_nf, serie = :serie, fornecedor_id = :fornecedor_id, 
                    data_emissao = :data_emissao, valor_total = :valor_total, icms = :icms, 
                    ipi = :ipi, pis = :pis, cofins = :cofins, centro_custo_id = :centro_custo_id
                WHERE id = :id AND tenant_id = :tenant_id
            ');

            $stmt->execute([
                'id' => $id,
                'tenant_id' => $this->getCurrentTenantId(),
                'numero_nf' => $numeroNf,
                'serie' => $serie ?: null,
                'fornecedor_id' => $fornecedorId,
                'data_emissao' => $dataEmissao,
                'valor_total' => $valorTotal,
                'icms' => $icms,
                'ipi' => $ipi,
                'pis' => $pis,
                'cofins' => $cofins,
                'centro_custo_id' => $centroCustoId,
            ]);

            return Response::json(['sucesso' => true, 'mensagem' => 'Nota fiscal atualizada com sucesso!']);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao atualizar nota fiscal.'], 500);
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
            $stmt = $this->pdo->prepare('DELETE FROM notas_entrada WHERE id = :id AND tenant_id = :tenant_id');
            $stmt->execute([
                'id' => $id,
                'tenant_id' => $this->getCurrentTenantId(),
            ]);

            if ($stmt->rowCount() === 0) {
                return Response::json(['sucesso' => false, 'erro' => 'Nota fiscal não encontrada.'], 404);
            }

            return Response::json(['sucesso' => true, 'mensagem' => 'Nota fiscal excluída com sucesso!']);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao excluir nota fiscal.'], 500);
        }
    }

    public function viewJson(Request $request): Response
    {
        $id = (int) $request->param('id', 0);

        try {
            $stmt = $this->pdo->prepare('
                SELECT ne.*, f.nome as fornecedor_nome, cc.nome as centro_custo_nome 
                FROM notas_entrada ne 
                LEFT JOIN fornecedores f ON ne.fornecedor_id = f.id
                LEFT JOIN centros_custo cc ON ne.centro_custo_id = cc.id
                WHERE ne.id = :id AND ne.tenant_id = :tenant_id
            ');
            $stmt->execute(['id' => $id, 'tenant_id' => $this->getCurrentTenantId()]);
            $nota = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nota) {
                return Response::json(['sucesso' => false, 'erro' => 'Nota fiscal não encontrada.'], 404);
            }

            return Response::json(['sucesso' => true, 'notaEntrada' => $nota]);
        } catch (\Exception $e) {
            return Response::json(['sucesso' => false, 'erro' => 'Erro ao buscar nota fiscal.'], 500);
        }
    }

    private function validarDados(Request $request): array
    {
        $erros = [];

        $numeroNf = trim((string) $request->input('numero_nf', ''));
        if (empty($numeroNf)) {
            $erros['numero_nf'] = 'Número da nota fiscal é obrigatório.';
        }

        $fornecedorId = (int) $request->input('fornecedor_id', 0);
        if ($fornecedorId <= 0) {
            $erros['fornecedor_id'] = 'Fornecedor é obrigatório.';
        }

        $dataEmissao = (string) $request->input('data_emissao', '');
        if (empty($dataEmissao)) {
            $erros['data_emissao'] = 'Data de emissão é obrigatória.';
        } else {
            try {
                new \DateTime($dataEmissao);
            } catch (\Exception $e) {
                $erros['data_emissao'] = 'Data de emissão inválida.';
            }
        }

        $valorTotal = (float) str_replace(',', '.', (string) $request->input('valor_total', 0));
        if ($valorTotal <= 0) {
            $erros['valor_total'] = 'Valor total deve ser maior que zero.';
        }

        return $erros;
    }

    private function getCurrentTenantId(): int
    {
        return 1;
    }
}
