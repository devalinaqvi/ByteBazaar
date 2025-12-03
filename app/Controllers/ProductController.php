<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ProductService;
use App\Core\Request;

class ProductController extends BaseController
{
    public ProductService $products;
    private Request $request;

    public function __construct(ProductService $products, Request $request)
    {
        parent::__construct();
        $this->products = $products;
        $this->request = $request;
    }

    public function index(): void
    {
        $categoryFilters = $this->request->query('categories') ?? [];

        $list = $this->products->listFiltered($categoryFilters);
        $categories = $this->products->categories();

        $this->render('pages/public_products', [
            'products'   => $list,
            'categories' => $categories,
            'selected'   => $categoryFilters,
            'title'      => 'Products'
        ]);
    }


    public function show(string $slug): void
    {
        $product = $this->products->getBySlug($slug);
        if (!$product) {
            $this->redirect('/products');
            return;
        }
        logMessage('Product: ' . $product->name . ' was viewed.');
        $this->render('pages/public_product_show', [
            'product' => $product,
            'title' => 'Product Details'
        ]);
    }

    public function edit(int $id): void
    {
        $product = $this->products->get($id);

        $this->render('admin/products/edit', [
            'product' => $product,
            'title' => 'Edit Product'
        ]);
    }

    public function update(int $id): void
    {
        $data = $this->request->all();
        $this->products->update($id, $data);

        $this->redirect('/admin/products');
    }

    public function delete(int $id): void
    {
        $this->products->delete($id);
        $this->redirect('/admin/products');
    }
    public function testIndexLoadsProducts()
    {
        $service = $this->createMock(ProductService::class);
        $service->method('list')->willReturn([]);

        $controller = new ProductController($service);

        ob_start();
        $controller->index(new Request());
        $output = ob_get_clean();

        $this->assertStringContainsString('Manage Products', $output);
    }

}
