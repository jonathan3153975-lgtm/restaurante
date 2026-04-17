<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'layouts/app'): void
    {
        $templatePath = App::basePath('views/' . $template . '.php');

        if (!is_file($templatePath)) {
            throw new RuntimeException(sprintf('View %s não encontrada.', $template));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        if ($layout === '') {
            echo $content;

            return;
        }

        $layoutPath = App::basePath('views/' . $layout . '.php');

        if (!is_file($layoutPath)) {
            throw new RuntimeException(sprintf('Layout %s não encontrado.', $layout));
        }

        require $layoutPath;
    }
}
