<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\UserService;

class AdminController extends BaseController
{
    private readonly Request $request;
    public function __construct(
        private readonly OrderService $orders,
        private readonly ProductService $products,
        private readonly CategoryService $categories,
        private readonly UserService $users,
        Request $request
    ) {
        parent::__construct();
        $this->request = $request;
    }
    public function index(): void {
        $orders = $this->orders->listAllOrders();
        $this->render('pages/admin', ['title' => 'Dashboard', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'orders' => $orders]);
    }
    public function viewUsers(): void {
        logMessage('Viewing users');
        $users = $this->users->listAllUsers();
        $this->render('pages/admin_users', ['title' => 'Users', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'users' => $users  ]);
    }
    public function search(Request $request): void
    {
        $q = $request->get('q', '');
        $page = (int)($request->get('page', 1));

        $result = $this->products->search($q, $page);

        $this->render('admin/products/index', $result);
    }

    public function products(): void {
        $list = $this->products->list();

        $this->render('pages/admin_products', ['title' => 'Products', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'products' => $list]);
    }
    public function editProduct(int $id): void
    {
        $categories = $this->categories->list();
        $product = $this->products->get($id);

        $this->render('pages/admin_edit_product', ['title' => 'Edit Product', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'product' => $product, 'categories' => $categories]);
    }
    public function showProduct(int $id): void
    {
        $product = $this->products->get($id);

        $this->render('pages/admin_show_product', ['title' => 'Product Details', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'product' => $product]);
    }
    public function updateProduct(int $id): void
    {
        try {
            $data = $this->request->all();
            $file = $_FILES['image'] ?? null;

            $this->products->update($id, $data, $file);

            $this->json([
                'success' => true,
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function createProduct(): void {
        $categories = $this->categories->list();
        $this->render('pages/admin_add_product', ['title' => 'Add Product', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'categories' => $categories]);
    }

    public function storeProduct(): void
    {
        try {
            $data = $this->request->all();
            $file = $_FILES['image_url'] ?? null;
            $productId = $this->products->create($data, $file);

            $this->json([
                'success' => true,
                'message' => 'Product created successfully',
                'productId' => $productId
            ], 201);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteProduct(int $id): void
    {
        $this->products->delete($id);
        $this->redirect('/admin/products');
    }

}