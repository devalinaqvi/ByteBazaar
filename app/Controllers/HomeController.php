<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CategoryService;
use App\Services\ProductService;

class HomeController extends BaseController
{

    private CategoryService $categoryService;
    private ProductService $products;

    public function __construct(CategoryService $categoryService, ProductService $productService) {
        parent::__construct();
        $this->categoryService = $categoryService;
        $this->products = $productService;
    }
    public function index(): void {
        $category = $this->categoryService->list();
        $products = $this->products->list();
        $this->render('pages/home', ['title' => 'Welcome to Computer Zone', 'categories' => $category, 'products' => $products]);
    }
}