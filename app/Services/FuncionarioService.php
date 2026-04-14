<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\FuncionarioRepository;

final class FuncionarioService
{
    public function __construct(private readonly FuncionarioRepository $repository)
    {
    }

    public function buscarFuncionarios(int $tenantId, string $filtro = ''): array
    {
        $filtro = trim($filtro);
        return $this->repository->searchByTenant($tenantId, $filtro);
    }
}
