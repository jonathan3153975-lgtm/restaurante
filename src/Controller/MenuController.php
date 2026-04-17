<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AbstractController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repository\MenuRepository;
use App\Service\MenuService;
use RuntimeException;

final class MenuController extends AbstractController
{
    public function __construct(
        private readonly MenuRepository $repository = new MenuRepository(),
        private readonly MenuService $service = new MenuService()
    ) {}

    public function index(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);

        $items = $this->repository->paginate([
            'search' => $request->query('search'),
            'category_id' => $request->query('category_id'),
            'page' => $request->query('page'),
        ]);

        $this->render('menu/index', [
            'items' => $items,
            'categories' => $this->repository->categories(),
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'category_id' => (string) $request->query('category_id', ''),
            ],
        ]);
    }

    public function create(): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->render('menu/form', [
            'categories' => $this->repository->categories(),
            'item' => null,
            'formAction' => '/admin/menu/store',
            'title' => 'Novo item do cardápio',
        ]);
    }

    public function edit(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $item = $this->repository->find((int) $request->param('id'));

        if ($item === null) {
            Session::flash('error', 'Item não encontrado.');
            $this->redirect('/admin/menu');
        }

        $this->render('menu/form', [
            'categories' => $this->repository->categories(),
            'item' => $item,
            'formAction' => '/admin/menu/' . $item['id'] . '/update',
            'title' => 'Editar item do cardápio',
        ]);
    }

    public function store(Request $request): void
    {
        $this->save($request);
    }

    public function update(Request $request): void
    {
        $this->save($request, (int) $request->param('id'));
    }

    public function delete(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);
        $this->repository->delete((int) $request->param('id'));
        Session::flash('success', 'Item removido do cardápio.');
        $this->redirect('/admin/menu');
    }

    public function quickStoreCategory(Request $request): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);

        $name = trim((string) $request->input('name'));
        $serviceGroup = trim((string) $request->input('service_group', 'meal'));

        if ($name === '') {
            Response::json(['message' => 'Informe o nome da categoria.'], 422);
        }

        $category = $this->repository->createCategory($name, $serviceGroup);
        Response::json(['message' => 'Categoria criada.', 'category' => $category]);
    }

    public function preview(): void
    {
        $this->requireAuth(['admin', 'manager', 'cashier']);
        $this->render('client/preview', [
            'menuByCategory' => $this->repository->publicMenu(),
        ], 'layouts/guest');
    }

    private function save(Request $request, ?int $id = null): void
    {
        $this->requireAuth(['admin', 'manager']);
        $this->validateCsrf($request);

        $existing = $id !== null ? $this->repository->find($id) : null;

        try {
            $data = $this->service->normalize($request->all(), $request->file('image'), $existing['image_path'] ?? null);
        } catch (RuntimeException $exception) {
            Session::setOld($request->all());
            Session::flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/menu/create' : '/admin/menu/' . $id . '/edit');
        }

        if ($data['category_id'] <= 0 || $data['title'] === '' || $data['sale_price'] <= 0) {
            Session::setOld($request->all());
            Session::flash('error', 'Preencha categoria, título e preço de venda.');
            $this->redirect($id === null ? '/admin/menu/create' : '/admin/menu/' . $id . '/edit');
        }

        if ($id === null) {
            $this->repository->save($data);
            Session::flash('success', 'Item cadastrado com sucesso.');
        } else {
            $this->repository->update($id, $data);
            Session::flash('success', 'Item atualizado com sucesso.');
        }

        $this->redirect('/admin/menu');
    }
}
