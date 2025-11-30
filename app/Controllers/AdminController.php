<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\OrderService;

class AdminController extends BaseController
{
    public function __construct(private readonly OrderService $orders) {
    }
    public function index(): void {
        $orders = $this->orders->listAllOrders();
        $this->render('pages/admin',
            [
                'title' => 'Dashboard',
                'body_class' => 'h-full',
                'html_class' => 'h-full bg-gray-100',
                'header' => true,
                'admin_nav_active' => 'active',
                'admin_footer_active' => 'active',
                'orders' => $orders
            ]);
    }
    public function products(): void {
        $this->render('pages/admin_products',
            ['title' => 'Products', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active']);
    }
    public function editProduct($id): void {
        $this->render('pages/admin_edit_product',
            ['title' => 'Edit Product', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active']);
    }
}