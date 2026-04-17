<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Database;
use PDO;

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }
}
