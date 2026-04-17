<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Repository\OrderRepository;
use App\Repository\TableRepository;

final class CashierController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $tableRepository = new TableRepository(),
        private readonly OrderRepository $orderRepository = new OrderRepository()
    ) {}

    public function index(): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);

        $this->render('cashier/index', [
            'sessions' => $this->tableRepository->sessionsForCashier(),
        ]);
    }

    public function checkout(Request $request): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);
        $this->validateCsrf($request);

        $paymentMethod = trim((string) $request->input('payment_method', 'pix'));
        $this->orderRepository->checkout((int) $request->param('sessionId'), $paymentMethod);

        Session::flash('success', 'Conta fechada com sucesso.');
        $this->redirect('/admin/cashier');
    }
}
