<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class TableRepository extends AbstractRepository
{
    public function paginate(array $filters): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $search = trim((string) ($filters['search'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = 'CAST(rt.number AS CHAR) LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($status === 'occupied') {
            $conditions[] = 'rt.active_session_id IS NOT NULL';
        }

        if ($status === 'free') {
            $conditions[] = 'rt.active_session_id IS NULL';
        }

        $where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countStatement = $this->pdo->prepare("SELECT COUNT(*) FROM restaurant_tables rt {$where}");
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = $this->pdo->prepare(
            "SELECT rt.*, ts.customer_name, ts.started_at,
                    TIMESTAMPDIFF(MINUTE, ts.started_at, NOW()) AS open_minutes,
                    (SELECT o.status FROM orders o WHERE o.table_session_id = ts.id ORDER BY o.created_at DESC LIMIT 1) AS order_status
             FROM restaurant_tables rt
             LEFT JOIN table_sessions ts ON ts.id = rt.active_session_id
             {$where}
             ORDER BY rt.number ASC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'data' => $statement->fetchAll(),
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'total' => $total,
        ];
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO restaurant_tables (number, seats, qr_token, is_active, created_at, updated_at) VALUES (:number, :seats, :qr_token, :is_active, NOW(), NOW())'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM restaurant_tables WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $table = $statement->fetch();

        return is_array($table) ? $table : null;
    }

    public function findByToken(string $token): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM restaurant_tables WHERE qr_token = :qr_token AND is_active = 1 LIMIT 1');
        $statement->execute(['qr_token' => $token]);
        $table = $statement->fetch();

        return is_array($table) ? $table : null;
    }

    public function activeSessionByToken(string $token): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT ts.*, rt.number AS table_number, rt.qr_token
             FROM table_sessions ts
             INNER JOIN restaurant_tables rt ON rt.id = ts.table_id
             WHERE rt.qr_token = :qr_token AND ts.status = 'open'
             LIMIT 1"
        );
        $statement->execute(['qr_token' => $token]);
        $session = $statement->fetch();

        return is_array($session) ? $session : null;
    }

    public function activeSessionByTableId(int $tableId): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT ts.*, rt.number AS table_number, rt.qr_token
             FROM table_sessions ts
             INNER JOIN restaurant_tables rt ON rt.id = ts.table_id
             WHERE rt.id = :table_id AND ts.status = 'open'
             LIMIT 1"
        );
        $statement->execute(['table_id' => $tableId]);
        $session = $statement->fetch();

        return is_array($session) ? $session : null;
    }

    public function orderableTables(): array
    {
        $statement = $this->pdo->query(
            "SELECT rt.id, rt.number, rt.seats, rt.is_active, rt.active_session_id, ts.customer_name
             FROM restaurant_tables rt
             LEFT JOIN table_sessions ts ON ts.id = rt.active_session_id
             WHERE rt.is_active = 1
             ORDER BY rt.number ASC"
        );

        return $statement->fetchAll();
    }

    public function openSession(int $tableId, string $customerName): int
    {
        $table = $this->find($tableId);

        if ($table !== null && $table['active_session_id'] !== null) {
            return (int) $table['active_session_id'];
        }

        $this->pdo->beginTransaction();

        $statement = $this->pdo->prepare('INSERT INTO table_sessions (table_id, customer_name, status, started_at, created_at, updated_at) VALUES (:table_id, :customer_name, :status, NOW(), NOW(), NOW())');
        $statement->execute([
            'table_id' => $tableId,
            'customer_name' => $customerName,
            'status' => 'open',
        ]);

        $sessionId = (int) $this->pdo->lastInsertId();

        $updateTable = $this->pdo->prepare('UPDATE restaurant_tables SET active_session_id = :active_session_id, updated_at = NOW() WHERE id = :id');
        $updateTable->execute([
            'active_session_id' => $sessionId,
            'id' => $tableId,
        ]);

        $this->pdo->commit();

        return $sessionId;
    }

    public function quickClose(int $tableId, string $paymentMethod): void
    {
        $table = $this->find($tableId);

        if ($table === null || $table['active_session_id'] === null) {
            return;
        }

        $sessionId = (int) $table['active_session_id'];
        $subtotalStatement = $this->pdo->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE table_session_id = :table_session_id');
        $subtotalStatement->execute(['table_session_id' => $sessionId]);
        $subtotal = (float) $subtotalStatement->fetchColumn();

        $this->pdo->beginTransaction();

        $payment = $this->pdo->prepare(
            "INSERT INTO payments (table_session_id, payment_method, amount, status, created_at, updated_at)
             VALUES (:table_session_id, :payment_method, :amount, 'paid', NOW(), NOW())"
        );
        $payment->execute([
            'table_session_id' => $sessionId,
            'payment_method' => $paymentMethod,
            'amount' => $subtotal,
        ]);

        $statement = $this->pdo->prepare(
            "UPDATE table_sessions SET status = 'closed', ended_at = NOW(), payment_method = :payment_method, updated_at = NOW() WHERE id = :id"
        );
        $statement->execute([
            'payment_method' => $paymentMethod,
            'id' => $sessionId,
        ]);

        $resetTable = $this->pdo->prepare('UPDATE restaurant_tables SET active_session_id = NULL, updated_at = NOW() WHERE id = :id');
        $resetTable->execute(['id' => $tableId]);

        $this->pdo->commit();
    }

    public function toggle(int $id): void
    {
        $statement = $this->pdo->prepare('UPDATE restaurant_tables SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM restaurant_tables WHERE id = :id AND active_session_id IS NULL');
        $statement->execute(['id' => $id]);
    }

    public function sessionsForCashier(): array
    {
        $statement = $this->pdo->query(
            "SELECT ts.id, ts.customer_name, ts.status, ts.payment_method, ts.started_at, ts.ended_at,
                    rt.number AS table_number,
                    COALESCE((SELECT SUM(total_amount) FROM orders o WHERE o.table_session_id = ts.id), 0) AS subtotal
             FROM table_sessions ts
             INNER JOIN restaurant_tables rt ON rt.id = ts.table_id
             ORDER BY ts.status ASC, ts.started_at DESC"
        );

        return $statement->fetchAll();
    }
}
