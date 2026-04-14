<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): string
    {
        $templatePath = dirname(__DIR__) . '/Views/' . $template . '.php';

        if (!file_exists($templatePath)) {
            return '<h1>Template nao encontrado: ' . htmlspecialchars($template, ENT_QUOTES, 'UTF-8') . '</h1>';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;
        return (string) ob_get_clean();
    }
}
