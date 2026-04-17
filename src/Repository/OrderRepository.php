<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class OrderRepository extends AbstractRepository
{
    public function openOrders(array $filters): array
    {
        $type = trim((string) ($filters['type'] ?? ''));

        $sql = "SELECT o.id, o.status, o.created_at, o.delivery_timing, o.total_amount,
                       rt.number AS table_number, ts.customer_name,
                       GROUP_CONCAT(mi.title SEPARATOR ', ') AS items,
                       MAX(c.service_group) AS service_group
                FROM orders o
                INNER JOIN table_sessions ts ON ts.id = o.table_session_id
                INNER JOIN restaurant_tables rt ON rt.id = ts.table_id
                INNER JOIN order_items oi ON oi.order_id = o.id
                INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
                INNER JOIN categories c ON c.id = mi.category_id
                WHERE o.status IN ('open', 'preparing')";

        $params = [];

        if ($type !== '') {
            $sql .= ' AND c.service_group = :service_group';
            $params['service_group'] = $type;
        }

        $sql .= ' GROUP BY o.id, rt.number, ts.customer_name ORDER BY o.created_at ASC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function markDelivered(int $id): void
    {
        $statement = $this->pdo->prepare("UPDATE orders SET status = 'delivered', delivered_at = NOW(), updated_at = NOW() WHERE id = :id");
        $statement->execute(['id' => $id]);
    }

    public function createOrder(int $sessionId, array $cart): int
    {
        $deliveryTiming = 'with_order';
        foreach ($cart as $cartItem) {
            if (($cartItem['delivery_timing'] ?? 'with_order') === 'immediate') {
                $deliveryTiming = 'immediate';
                break;
            }
        }

        $this->pdo->beginTransaction();

        $orderStatement = $this->pdo->prepare(
            "INSERT INTO orders (table_session_id, status, delivery_timing, total_amount, created_at, updated_at)
             VALUES (:table_session_id, 'open', :delivery_timing, :total_amount, NOW(), NOW())"
        );
        $orderStatement->execute([
            'table_session_id' => $sessionId,
            'delivery_timing' => $deliveryTiming,
            'total_amount' => $this->cartTotal($cart),
        ]);

        $orderId = (int) $this->pdo->lastInsertId();

        $itemStatement = $this->pdo->prepare(
            'INSERT INTO order_items (order_id, menu_item_id, title_snapshot, quantity, unit_price, removed_ingredients, additionals, notes, created_at, updated_at)
             VALUES (:order_id, :menu_item_id, :title_snapshot, :quantity, :unit_price, :removed_ingredients, :additionals, :notes, NOW(), NOW())'
        );

        $stockStatement = $this->pdo->prepare('UPDATE menu_items SET stock_quantity = stock_quantity - :quantity WHERE id = :id AND is_stockable = 1');

        foreach ($cart as $cartItem) {
            $itemStatement->execute([
                'order_id' => $orderId,
                'menu_item_id' => $cartItem['menu_item_id'],
                'title_snapshot' => $cartItem['title'],
                'quantity' => $cartItem['quantity'],
                'unit_price' => $cartItem['unit_price'],
                'removed_ingredients' => json_encode($cartItem['removed_ingredients'], JSON_UNESCAPED_UNICODE),
                'additionals' => json_encode($cartItem['additionals'], JSON_UNESCAPED_UNICODE),
                'notes' => $cartItem['notes'],
            ]);

            $stockStatement->execute([
                'quantity' => $cartItem['quantity'],
                'id' => $cartItem['menu_item_id'],
            ]);
        }

        $this->pdo->commit();

        return $orderId;
    }

    public function sessionOrders(int $sessionId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT o.id, o.status, o.delivery_timing, o.total_amount, o.created_at,
                    GROUP_CONCAT(oi.title_snapshot SEPARATOR ', ') AS items
             FROM orders o
             INNER JOIN order_items oi ON oi.order_id = o.id
             WHERE o.table_session_id = :table_session_id
             GROUP BY o.id
             ORDER BY o.created_at DESC"
        );
        $statement->execute(['table_session_id' => $sessionId]);

        return $statement->fetchAll();
    }

    public function sessionSubtotal(int $sessionId): float
    {
        $statement = $this->pdo->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE table_session_id = :table_session_id');
        $statement->execute(['table_session_id' => $sessionId]);

        return (float) $statement->fetchColumn();
    }

    public function createPaymentRequest(int $sessionId, string $method, float $amount): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO payments (table_session_id, payment_method, amount, status, created_at, updated_at)
             VALUES (:table_session_id, :payment_method, :amount, 'requested', NOW(), NOW())"
        );
        $statement->execute([
            'table_session_id' => $sessionId,
            'payment_method' => $method,
            'amount' => $amount,
        ]);
    }

    public function checkout(int $sessionId, string $paymentMethod): void
    {
        $subtotal = $this->sessionSubtotal($sessionId);

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

        $session = $this->pdo->prepare(
            "UPDATE table_sessions SET status = 'closed', ended_at = NOW(), payment_method = :payment_method, updated_at = NOW() WHERE id = :id"
        );
        $session->execute([
            'payment_method' => $paymentMethod,
            'id' => $sessionId,
        ]);

        $tableReset = $this->pdo->prepare(
            'UPDATE restaurant_tables SET active_session_id = NULL, updated_at = NOW() WHERE active_session_id = :active_session_id'
        );
        $tableReset->execute(['active_session_id' => $sessionId]);

        $this->pdo->commit();
    }

    private function cartTotal(array $cart): float
    {
        $total = 0.0;

        foreach ($cart as $cartItem) {
            $additionals = array_sum(array_map(static fn (array $item): float => (float) ($item['price'] ?? 0), $cartItem['additionals']));
            $total += ((float) $cartItem['unit_price'] + $additionals) * (int) $cartItem['quantity'];
        }

        return $total;
    }
}
