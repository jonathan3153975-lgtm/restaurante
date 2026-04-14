<?php

declare(strict_types=1);

namespace App\Core;

final class ModuleData
{
    public static function adminNfs(): array
    {
        return [
            ['numero' => 'NF-2026-1001', 'fornecedor' => 'Distribuidora Sul', 'valor' => 1890.55, 'status' => 'Importada'],
            ['numero' => 'NF-2026-1002', 'fornecedor' => 'Laticinios Minas', 'valor' => 760.10, 'status' => 'Lancada'],
        ];
    }

    public static function estoqueProdutos(): array
    {
        return [
            ['codigo' => 'EST-001', 'nome' => 'Arroz Branco', 'unidade' => 'kg', 'saldo' => 43, 'minimo' => 15],
            ['codigo' => 'EST-002', 'nome' => 'File de Frango', 'unidade' => 'kg', 'saldo' => 9, 'minimo' => 12],
        ];
    }

    public static function rhFuncionarios(): array
    {
        return [
            ['nome' => 'Marcos Silva', 'cpf' => '12345678901', 'telefone' => '11999998888', 'cargo' => 'Cozinheiro'],
            ['nome' => 'Ana Souza', 'cpf' => '98765432100', 'telefone' => '11988887777', 'cargo' => 'Caixa'],
        ];
    }

    public static function financeiroMovimentos(): array
    {
        return [
            ['descricao' => 'Venda balcao', 'tipo' => 'Entrada', 'valor' => 420.90],
            ['descricao' => 'Compra de insumos', 'tipo' => 'Saida', 'valor' => 198.45],
        ];
    }

    public static function cardapioItens(): array
    {
        return [
            ['item' => 'Hamburguer Artesanal', 'categoria' => 'Lanches', 'preco' => 34.90, 'disponivel' => 'Sim'],
            ['item' => 'Suco de Laranja', 'categoria' => 'Bebidas', 'preco' => 12.50, 'disponivel' => 'Sim'],
        ];
    }

    public static function mesasPedidos(): array
    {
        return [
            ['mesa' => '05', 'pedido' => '#10021', 'status' => 'Pendente', 'total' => 89.70],
            ['mesa' => '02', 'pedido' => '#10022', 'status' => 'Em preparo', 'total' => 54.20],
        ];
    }

    public static function cozinhaPedidos(): array
    {
        return [
            ['pedido' => '#10021', 'itens' => '2x Hamburguer + 1x Batata', 'status' => 'Pendente'],
            ['pedido' => '#10022', 'itens' => '1x Salada Caesar', 'status' => 'Em preparo'],
        ];
    }
}
