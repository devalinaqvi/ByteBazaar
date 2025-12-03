<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private readonly AuthService $authService;
    private readonly Request $request;

    public function __construct(AuthService $authService, Request $request) {
        parent::__construct();
        $this->authService = $authService;
        $this->request = $request;
    }
    public function login(): void {
        $this->render('pages/login', ['title' => 'Login', 'nav_rendered' => false, 'footer_rendered' => false, 'body_class' => 'h-full', 'html_class' => 'bg-gray-100']);
    }

    public function register(): void {
        $this->render('pages/register', ['title' => 'Register', 'nav_rendered' => false, 'footer_rendered' => false, 'body_class' => 'h-full', 'html_class' => 'bg-gray-100']);
    }

    public function postRegister(): void {
        $data = $this->request->all();
        $email = $data['email'];
        if ($this->authService->emailExists($email)) {
            $this->json([
                'success' => false,
                'message' => 'Email already exists'
            ]);
        }
        $this->authService->register($data);
        $this->redirect('/login');
    }

    public function logout(): void {
        $this->authService->logout();
        $this->redirect('/login');
    }

    public function forgotPassword(): void {
        $this->render('pages/forgot-password', ['title' => 'Forgot Password', 'nav_rendered' => false, 'footer_rendered' => false, 'body_class' => 'h-full', 'html_class' => 'bg-gray-100']);
    }
    public function authenticate(): void {
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $authenticatedUser = $this->authService->authenticate($email, $password);
        logMessage("Authenticating user with email: {$email}");
        logMessage("Result: " . ($authenticatedUser ? 'success' : 'failure'));
        if (!$authenticatedUser) {
            $this->redirect('/login');
            return;
        }
        $this->authService->login($authenticatedUser);
        if($authenticatedUser->is_admin)
            $this->redirect('/admin');
        else
            $this->redirect('/user/dashboard');
    }
    public function resetPassword(): void {}

    public function verify(): void {}
}