<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            ['label' => 'Receita do dia', 'value' => 'R$ 8.420', 'trend' => '+18%', 'icon' => 'bi-cash-stack'],
            ['label' => 'Pedidos ativos', 'value' => '27', 'trend' => '+5 mesas', 'icon' => 'bi-bag-check'],
            ['label' => 'Tempo médio', 'value' => '14 min', 'trend' => '-2 min', 'icon' => 'bi-stopwatch'],
            ['label' => 'NPS salão', 'value' => '94', 'trend' => '+6 pts', 'icon' => 'bi-stars'],
        ];

        $tables = [
            ['table' => 'Mesa 01', 'status' => 'Aguardando pagamento', 'amount' => 'R$ 182,00', 'channel' => 'QR-Code'],
            ['table' => 'Mesa 04', 'status' => 'Pedido em preparo', 'amount' => 'R$ 96,50', 'channel' => 'Garçom'],
            ['table' => 'Mesa 08', 'status' => 'Conta parcial', 'amount' => 'R$ 241,90', 'channel' => 'QR-Code'],
            ['table' => 'Mesa 12', 'status' => 'Pronta para liberar', 'amount' => 'R$ 68,00', 'channel' => 'Caixa'],
        ];

        $timeline = [
            ['time' => '19:05', 'title' => 'Pedido #431 enviado para cozinha', 'text' => '2 massas, 1 salada premium, observação sem glúten.'],
            ['time' => '19:11', 'title' => 'Pagamento Pix iniciado', 'text' => 'Mesa 01 solicitou fechamento da conta pelo QR-Code.'],
            ['time' => '19:17', 'title' => 'Reposição de estoque sugerida', 'text' => 'Filé mignon atingiu estoque mínimo para o turno da noite.'],
        ];

        $this->render('dashboard/index', [
            'stats' => $stats,
            'tables' => $tables,
            'timeline' => $timeline,
        ]);
    }
}