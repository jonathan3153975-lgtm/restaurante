<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\MenuRepository;
use RuntimeException;

final class ClientService
{
    public function __construct(
        private readonly MenuRepository $menuRepository = new MenuRepository()
    ) {}

    public function parseCart(string $json): array
    {
        $rawItems = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($rawItems) || $rawItems === []) {
            throw new RuntimeException('Adicione ao menos um item ao pedido.');
        }

        $menuIndex = [];
        foreach ($this->menuRepository->publicMenu() as $items) {
            foreach ($items as $item) {
                $menuIndex[(int) $item['id']] = $item;
            }
        }

        $cart = [];

        foreach ($rawItems as $rawItem) {
            $menuItemId = (int) ($rawItem['menu_item_id'] ?? 0);
            $menuItem = $menuIndex[$menuItemId] ?? null;

            if ($menuItem === null) {
                continue;
            }

            $additionals = [];
            foreach ($rawItem['additionals'] ?? [] as $additional) {
                $additionals[] = [
                    'name' => trim((string) ($additional['name'] ?? '')),
                    'price' => (float) ($additional['price'] ?? 0),
                ];
            }

            $cart[] = [
                'menu_item_id' => $menuItemId,
                'title' => $menuItem['title'],
                'quantity' => max(1, (int) ($rawItem['quantity'] ?? 1)),
                'unit_price' => (float) $menuItem['sale_price'],
                'removed_ingredients' => array_values(array_filter(array_map('strval', $rawItem['removed_ingredients'] ?? []))),
                'additionals' => $additionals,
                'notes' => trim((string) ($rawItem['notes'] ?? '')),
                'delivery_timing' => trim((string) ($rawItem['delivery_timing'] ?? 'with_order')) === 'immediate' ? 'immediate' : 'with_order',
            ];
        }

        if ($cart === []) {
            throw new RuntimeException('Nenhum item válido foi enviado para o pedido.');
        }

        return $cart;
    }
}
