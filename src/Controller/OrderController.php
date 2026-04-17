<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Repository\OrderRepository;
use App\Repository\TableRepository;
use App\Service\ClientService;
use RuntimeException;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $repository = new OrderRepository(),
        private readonly TableRepository $tableRepository = new TableRepository(),
        private readonly ClientService $clientService = new ClientService()
    ) {}

    public function index(Request $request): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);

        $this->render('orders/index', [
            'orders' => $this->repository->openOrders([
                'type' => $request->query('type'),
            ]),
            'selectedType' => (string) $request->query('type', ''),
        ]);
    }

    public function markDelivered(Request $request): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);
        $this->validateCsrf($request);
        $this->repository->markDelivered((int) $request->param('id'));
        Session::flash('success', 'Pedido marcado como entregue.');
        $this->redirect('/admin/orders');
    }

    public function store(Request $request): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);
        $this->validateCsrf($request);

        $redirectTo = $this->sanitizeRedirect((string) $request->input('redirect_to', '/admin/orders'));
        $tableId = (int) $request->input('table_id');

        try {
            $table = $this->tableRepository->find($tableId);

            if ($table === null || (int) ($table['is_active'] ?? 0) !== 1) {
                throw new RuntimeException('Selecione uma mesa ativa para gerar o pedido.');
            }

            $session = $this->tableRepository->activeSessionByTableId($tableId);
            $sessionId = $session !== null ? (int) $session['id'] : 0;

            if ($sessionId === 0) {
                $customerName = trim((string) $request->input('customer_name'));

                if ($customerName === '') {
                    throw new RuntimeException('Informe o nome do cliente para abrir a mesa.');
                }

                $sessionId = $this->tableRepository->openSession($tableId, $customerName);
            }

            $cart = $this->clientService->parseCart((string) $request->input('cart_payload', '[]'));
            $this->repository->createOrder($sessionId, $cart);

            Session::flash('success', 'Pedido gerado com sucesso.');
        } catch (RuntimeException | \JsonException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->redirect($redirectTo);
    }

    private function sanitizeRedirect(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/')) {
            return '/admin/orders';
        }

        return $path;
    }
}
