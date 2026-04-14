<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): Response
    {
        return Response::html(View::render($template, $data));
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect($path);
    }
}
