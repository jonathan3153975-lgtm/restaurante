<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'app'): void
    {
        $viewFile = BASE_PATH . '/views/' . $template . '.php';
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException(sprintf('View "%s" not found.', $template));
        }

        if (!is_file($layoutFile)) {
            throw new \RuntimeException(sprintf('Layout "%s" not found.', $layout));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require $layoutFile;
    }
}