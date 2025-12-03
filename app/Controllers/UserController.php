<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\UserService;

class UserController extends BaseController
{
    private readonly Request $request;
    public function __construct(
        private readonly UserService $users,
        Request $request
    ) {
        parent::__construct();
    }

    public function dashboard(): void {
        $userId = $_SESSION['user_id'];
        $orders = $this->users->listAllOrders($userId);
        $this->render('pages/user/index', ['title' => 'Dashboard', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_user' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'orders' => $orders]);
    }
}