<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $template, array $data = [], string $layout = 'app'): void
    {
        View::render($template, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }
}