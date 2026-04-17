<?php

declare(strict_types=1);

namespace App\Repository;

final class DashboardRepository extends AbstractRepository
{
    public function overview(): array
    {
        return [
            'menu_items' => (int) $this->pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn(),
            'active_tables' => (int) $this->pdo->query("SELECT COUNT(*) FROM restaurant_tables WHERE active_session_id IS NOT NULL")->fetchColumn(),
            'open_orders' => (int) $this->pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('open', 'preparing')")->fetchColumn(),
            'today_revenue' => (float) $this->pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE DATE(created_at) = CURDATE() AND status = 'paid'")->fetchColumn(),
        ];
    }

    public function recentOrders(): array
    {
        $statement = $this->pdo->query(
            "SELECT o.id, o.status, o.created_at, rt.number AS table_number, ts.customer_name, o.total_amount
             FROM orders o
             INNER JOIN table_sessions ts ON ts.id = o.table_session_id
             INNER JOIN restaurant_tables rt ON rt.id = ts.table_id
             ORDER BY o.created_at DESC
             LIMIT 8"
        );

        return $statement->fetchAll();
    }
}
