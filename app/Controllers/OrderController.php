<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\OrderService;
use App\Services\ProductService;

class OrderController extends BaseController
{
    public OrderService $orders;
    public ProductService $products;
    private Request $request;
    public function __construct(OrderService $orders, ProductService $productService, Request $request)
    {
        parent::__construct();
        $this->orders = $orders;
        $this->products = $productService;
        $this->request = $request;
    }
    public function index(): void
    {
        $this->render('pages/order', ['title' => 'Order']);
    }
    public function checkout(): void
    {
        $this->render('pages/checkout', ['title' => 'Checkout']);
    }
}