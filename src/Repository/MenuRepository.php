<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class MenuRepository extends AbstractRepository
{
    public function paginate(array $filters): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $search = trim((string) ($filters['search'] ?? ''));
        $categoryId = (int) ($filters['category_id'] ?? 0);

        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = '(mi.title LIKE :search OR mi.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $conditions[] = 'mi.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countStatement = $this->pdo->prepare("SELECT COUNT(*) FROM menu_items mi {$where}");
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $statement = $this->pdo->prepare(
            "SELECT mi.*, c.name AS category_name
             FROM menu_items mi
             INNER JOIN categories c ON c.id = mi.category_id
             {$where}
             ORDER BY mi.is_active DESC, mi.title ASC
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

    public function categories(): array
    {
        return $this->pdo
            ->query('SELECT id, name, service_group FROM categories ORDER BY name ASC')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM menu_items WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $item = $statement->fetch();

        return is_array($item) ? $item : null;
    }

    public function createCategory(string $name, string $serviceGroup): array
    {
        $statement = $this->pdo->prepare('INSERT INTO categories (name, service_group, created_at, updated_at) VALUES (:name, :service_group, NOW(), NOW())');
        $statement->execute([
            'name' => $name,
            'service_group' => $serviceGroup,
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return [
            'id' => $id,
            'name' => $name,
            'service_group' => $serviceGroup,
        ];
    }

    public function save(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO menu_items
            (category_id, title, description, removable_ingredients, additionals, cost_price, sale_price, image_path, image_zoom, image_position_x, image_position_y, is_stockable, stock_quantity, is_active, created_at, updated_at)
            VALUES
            (:category_id, :title, :description, :removable_ingredients, :additionals, :cost_price, :sale_price, :image_path, :image_zoom, :image_position_x, :image_position_y, :is_stockable, :stock_quantity, :is_active, NOW(), NOW())'
        );

        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = $this->pdo->prepare(
            'UPDATE menu_items SET
                category_id = :category_id,
                title = :title,
                description = :description,
                removable_ingredients = :removable_ingredients,
                additionals = :additionals,
                cost_price = :cost_price,
                sale_price = :sale_price,
                image_path = :image_path,
                image_zoom = :image_zoom,
                image_position_x = :image_position_x,
                image_position_y = :image_position_y,
                is_stockable = :is_stockable,
                stock_quantity = :stock_quantity,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id'
        );

        $statement->execute($data);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM menu_items WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function publicMenu(): array
    {
        $statement = $this->pdo->query(
            "SELECT mi.*, c.name AS category_name, c.service_group
             FROM menu_items mi
             INNER JOIN categories c ON c.id = mi.category_id
             WHERE mi.is_active = 1
             ORDER BY c.name ASC, mi.title ASC"
        );

        $items = $statement->fetchAll();
        $grouped = [];

        foreach ($items as $item) {
            $grouped[$item['category_name']][] = $item;
        }

        return $grouped;
    }
}
