<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

abstract class BaseRepository
{
    public function __construct(protected readonly PDO $pdo)
    {
    }

    protected function tenantWhere(string $prefix = ''): string
    {
        return ($prefix !== '' ? $prefix . '.' : '') . 'tenant_id = :tenant_id';
    }
}
