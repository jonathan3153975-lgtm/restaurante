<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use App\Repository\TableRepository;
use App\Service\ClientService;
use RuntimeException;

final class ClientController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $tableRepository = new TableRepository(),
        private readonly MenuRepository $menuRepository = new MenuRepository(),
        private readonly OrderRepository $orderRepository = new OrderRepository(),
        private readonly ClientService $clientService = new ClientService()
    ) {}

    public function show(Request $request): void
    {
        $token = (string) $request->param('token');
        $table = $this->tableRepository->findByToken($token);
        $session = $this->tableRepository->activeSessionByToken($token);

        if ($table === null) {
            $this->render('client/unavailable', ['message' => 'Mesa não encontrada ou indisponível.'], 'layouts/guest');
            return;
        }

        $sessionOrders = $session !== null ? $this->orderRepository->sessionOrders((int) $session['id']) : [];
        $subtotal = $session !== null ? $this->orderRepository->sessionSubtotal((int) $session['id']) : 0.0;

        $this->render('client/show', [
            'table' => $table,
            'session' => $session,
            'menuByCategory' => $this->menuRepository->publicMenu(),
            'sessionOrders' => $sessionOrders,
            'subtotal' => $subtotal,
            'tableToken' => $token,
        ], 'layouts/guest');
    }

    public function register(Request $request): void
    {
        $this->validateCsrf($request);

        $token = (string) $request->param('token');
        $table = $this->tableRepository->findByToken($token);

        if ($table === null) {
            Session::flash('error', 'Mesa indisponível para registro.');
            $this->redirect('/');
        }

        if ($this->tableRepository->activeSessionByToken($token) === null) {
            $customerName = trim((string) $request->input('customer_name'));

            if ($customerName === '') {
                Session::flash('error', 'Informe seu nome para abrir a mesa.');
                $this->redirect('/mesa/' . $token);
            }

            $this->tableRepository->openSession((int) $table['id'], $customerName);
            Session::flash('success', 'Mesa liberada. Escolha seus itens e envie o pedido.');
        }

        $this->redirect('/mesa/' . $token);
    }

    public function placeOrder(Request $request): void
    {
        $this->validateCsrf($request);

        $token = (string) $request->param('token');
        $session = $this->tableRepository->activeSessionByToken($token);

        if ($session === null) {
            Session::flash('error', 'A mesa ainda não foi iniciada.');
            $this->redirect('/mesa/' . $token);
        }

        try {
            $cart = $this->clientService->parseCart((string) $request->input('cart_payload', '[]'));
            $this->orderRepository->createOrder((int) $session['id'], $cart);
            Session::flash('success', 'Pedido enviado para a equipe.');
        } catch (RuntimeException|\JsonException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        $this->redirect('/mesa/' . $token);
    }

    public function requestPayment(Request $request): void
    {
        $this->validateCsrf($request);

        $token = (string) $request->param('token');
        $session = $this->tableRepository->activeSessionByToken($token);

        if ($session === null) {
            Session::flash('error', 'Nenhuma conta aberta para esta mesa.');
            $this->redirect('/mesa/' . $token);
        }

        $method = trim((string) $request->input('payment_method', 'pix'));
        $amount = $this->orderRepository->sessionSubtotal((int) $session['id']);
        $this->orderRepository->createPaymentRequest((int) $session['id'], $method, $amount);
        Session::flash('success', 'Solicitação de pagamento enviada ao caixa.');
        $this->redirect('/mesa/' . $token);
    }
}