<?php
namespace App\Controllers;
use App\Core\{BaseController, Request};
use App\Services\{OrderService, ProductService, CategoryService, UserService};
class AdminController extends BaseController
{
    public function __construct(
        private OrderService $ordersService,
        private ProductService $productsService,
        private CategoryService $categories,
        private UserService $users,
        private Request $request,
    ) {}
    public function index(): void
    {
        $this->orders();
    }
    public function orders(): void
    {
        $this->render(
            "pages/orders",
            array_merge(
                ["title" => "Store overview", "is_admin" => true],
                $this->ordersService->listing(
                    null,
                    max(1, (int) $this->request->get("page", 1)),
                ),
            ),
        );
    }
    public function products(): void
    {
        $q = $this->request->get("q", "");
        $this->render(
            "pages/admin/admin_products",
            array_merge(
                [
                    "title" => "Inventory",
                    "is_admin" => true,
                    "q" => is_string($q) ? $q : "",
                ],
                $this->productsService->catalog(
                    is_string($q) ? substr($q, 0, 200) : "",
                    [],
                    max(1, (int) $this->request->get("page", 1)),
                ),
            ),
        );
    }
    public function viewUsers(): void
    {
        $page = max(1, (int) $this->request->get("page", 1));
        $this->render(
            "pages/admin/admin_users",
            array_merge(
                ["title" => "Customers", "is_admin" => true],
                $this->users->listing($page),
            ),
        );
    }
    public function createProduct(): void
    {
        $this->render("pages/admin/product_form", [
            "title" => "Add product",
            "is_admin" => true,
            "categories" => $this->categories->list(),
            "product" => null,
        ]);
    }
    public function editProduct(int $id): void
    {
        $this->render("pages/admin/product_form", [
            "title" => "Edit product",
            "is_admin" => true,
            "categories" => $this->categories->list(),
            "product" => $this->productsService->get($id),
        ]);
    }
    public function storeProduct(): void
    {
        $this->productsService->create(
            $this->request->all(),
            $this->request->file("image"),
        );
        $this->done();
    }
    public function updateProduct(int $id): void
    {
        $this->productsService->update(
            $id,
            $this->request->all(),
            $this->request->file("image"),
        );
        $this->done();
    }
    public function deleteProduct(int $id): void
    {
        $this->productsService->delete($id);
        $this->done();
    }
    private function done(): void
    {
        if (wants_json()) {
            $this->json([
                "success" => true,
                "message" => "Inventory updated.",
                "redirect" => url_path("admin/products"),
            ]);
        } else {
            $this->redirect("/admin/products");
        }
    }
}
