<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Repository\DashboardRepository;
use App\Repository\MenuRepository;
use App\Repository\TableRepository;

final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly ?DashboardRepository $repository = null
    ) {}

    public function home(): void
    {
        $this->redirect(current_user() !== null ? '/admin' : '/login');
    }

    public function index(): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);

        $repository = $this->repository ?? new DashboardRepository();
        $tableRepository = new TableRepository();
        $menuRepository = new MenuRepository();

        $this->render('dashboard/index', [
            'stats' => $repository->overview(),
            'recentOrders' => $repository->recentOrders(),
            'tableOptions' => $tableRepository->orderableTables(),
            'menuByCategory' => $menuRepository->publicMenu(),
        ]);
    }
}
