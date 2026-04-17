<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Session;
use App\Repository\MenuRepository;
use App\Repository\TableRepository;
use App\Service\QrCodeService;

final class TableController extends AbstractController
{
    public function __construct(
        private readonly TableRepository $repository = new TableRepository(),
        private readonly QrCodeService $qrCodeService = new QrCodeService()
    ) {}

    public function index(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);

        $tables = $this->repository->paginate([
            'search' => $request->query('search'),
            'status' => $request->query('status'),
            'page' => $request->query('page'),
        ]);

        $qrCodes = [];
        foreach ($tables['data'] as $table) {
            $qrCodes[(int) $table['id']] = $this->qrCodeService->imageUrl($table['qr_token']);
        }

        $this->render('tables/index', [
            'tables' => $tables,
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'status' => (string) $request->query('status', ''),
            ],
            'qrCodes' => $qrCodes,
            'tableOptions' => $this->repository->orderableTables(),
            'menuByCategory' => (new MenuRepository())->publicMenu(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->render('tables/form');
    }

    public function store(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);

        $number = (int) $request->input('number');
        $seats = (int) $request->input('seats');

        if ($number <= 0 || $seats <= 0) {
            Session::flash('error', 'Informe um número de mesa e a quantidade de cadeiras.');
            $this->redirect('/admin/tables/create');
        }

        $this->repository->create([
            'number' => $number,
            'seats' => $seats,
            'qr_token' => bin2hex(random_bytes(16)),
            'is_active' => $request->input('is_active') !== null ? 1 : 0,
        ]);

        Session::flash('success', 'Mesa cadastrada com QR-Code exclusivo.');
        $this->redirect('/admin/tables');
    }

    public function occupy(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);

        $tableId = (int) $request->param('id');
        $customerName = trim((string) $request->input('customer_name'));

        if ($customerName === '') {
            Session::flash('error', 'Informe o nome do cliente para iniciar o atendimento.');
            $this->redirect('/admin/tables');
        }

        $this->repository->openSession($tableId, $customerName);
        Session::flash('success', 'Mesa ocupada e pronta para novos pedidos.');
        $this->redirect('/admin/tables');
    }

    public function close(Request $request): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);
        $this->validateCsrf($request);
        $paymentMethod = trim((string) $request->input('payment_method', 'pix'));
        $this->repository->quickClose((int) $request->param('id'), $paymentMethod);
        Session::flash('success', 'Mesa encerrada.');
        $this->redirect('/admin/tables');
    }

    public function toggle(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);
        $this->repository->toggle((int) $request->param('id'));
        Session::flash('success', 'Status da mesa atualizado.');
        $this->redirect('/admin/tables');
    }

    public function delete(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);
        $this->repository->delete((int) $request->param('id'));
        Session::flash('success', 'Mesa removida.');
        $this->redirect('/admin/tables');
    }
}
