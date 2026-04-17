<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\App;
use RuntimeException;

final class MenuService
{
    public function normalize(array $data, ?array $file, ?string $currentImage = null): array
    {
        $imagePath = $currentImage;
        $isStockable = isset($data['is_stockable']);

        if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                throw new RuntimeException('Falha ao enviar a imagem do item.');
            }

            $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($extension, $allowed, true)) {
                throw new RuntimeException('Envie imagens em JPG, PNG ou WEBP.');
            }

            $fileName = uniqid('menu_', true) . '.' . $extension;
            $destination = App::config('app.upload_dir') . '/' . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new RuntimeException('Não foi possível salvar a imagem do item.');
            }

            $imagePath = '/uploads/' . $fileName;
        }

        return [
            'category_id' => (int) ($data['category_id'] ?? 0),
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'removable_ingredients' => json_encode($this->normalizeIngredients($data['removable_ingredients'] ?? []), JSON_UNESCAPED_UNICODE),
            'additionals' => json_encode($this->normalizeAdditionals($data), JSON_UNESCAPED_UNICODE),
            'cost_price' => $this->currencyToFloat((string) ($data['cost_price'] ?? '0')),
            'sale_price' => $this->currencyToFloat((string) ($data['sale_price'] ?? '0')),
            'image_path' => $imagePath,
            'image_zoom' => max(100, min(180, (int) ($data['image_zoom'] ?? 100))),
            'image_position_x' => max(0, min(100, (int) ($data['image_position_x'] ?? 50))),
            'image_position_y' => max(0, min(100, (int) ($data['image_position_y'] ?? 50))),
            'is_stockable' => $isStockable ? 1 : 0,
            'stock_quantity' => $isStockable ? max(0, (int) ($data['stock_quantity'] ?? 0)) : 0,
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];
    }

    private function normalizeIngredients(array|string $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(static fn (mixed $item): string => trim((string) $item), $value)));
        }

        $items = preg_split('/[\r\n,]+/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $items)));
    }

    private function normalizeAdditionals(array $data): array
    {
        $names = $data['additional_names'] ?? null;
        $prices = $data['additional_prices'] ?? null;

        if (is_array($names)) {
            $additionals = [];

            foreach ($names as $index => $name) {
                $trimmedName = trim((string) $name);

                if ($trimmedName === '') {
                    continue;
                }

                $additionals[] = [
                    'name' => $trimmedName,
                    'price' => $this->currencyToFloat((string) ($prices[$index] ?? '0')),
                ];
            }

            return $additionals;
        }

        return $this->parseAdditionals((string) ($data['additionals'] ?? ''));
    }

    private function parseAdditionals(string $value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];
        $additionals = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            [$name, $price] = array_pad(array_map('trim', explode('|', $trimmed, 2)), 2, '0');
            $additionals[] = [
                'name' => $name,
                'price' => $this->currencyToFloat($price),
            ];
        }

        return $additionals;
    }

    private function currencyToFloat(string $value): float
    {
        $normalized = str_replace(['.', ','], ['', '.'], preg_replace('/[^\d,.-]/', '', $value) ?? '0');

        return (float) $normalized;
    }
}
