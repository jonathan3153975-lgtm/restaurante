<?php

declare(strict_types=1);

namespace App\Repositories;

final class FuncionarioRepository extends BaseRepository
{
    public function searchByTenant(int $tenantId, string $term = ''): array
    {
        $sql = 'SELECT nome, cpf, telefone, cargo FROM funcionarios WHERE ' . $this->tenantWhere() . ' AND ativo = 1';

        if ($term !== '') {
            $sql .= ' AND (nome LIKE :term OR cpf LIKE :term OR telefone LIKE :term OR cargo LIKE :term)';
        }

        $sql .= ' ORDER BY nome ASC LIMIT 100';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tenant_id', $tenantId, \PDO::PARAM_INT);

        if ($term !== '') {
            $stmt->bindValue(':term', '%' . $term . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}
