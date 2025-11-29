<?php

namespace App\Controllers;

use App\Core\BaseController;

class HomeController extends BaseController
{

    public function index(): void {
        $this->render('pages/home', ['title' => 'Welcome to Byte Bazaar']);
    }
}