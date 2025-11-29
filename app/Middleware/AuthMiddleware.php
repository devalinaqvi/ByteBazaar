<?php
namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware extends BaseMiddleware {
    private bool $guestOnly;  // True: redirect if logged in (e.g., login page)

    public function __construct(AuthService $authService, bool $guestOnly = false) {
        parent::__construct($authService);
        $this->guestOnly = $guestOnly;
    }

    public function handle($request, \Closure $next): void {
        $user = $this->authService->getCurrentUser();
        if ($this->guestOnly) {
            if ($user) {
                $this->redirect('/products');  // Logged in? Skip to home
            }
        } else {
            if (!$user) {
                $this->redirect('/login', ['redirect' => $request->getUri()]);  // Save return URL
            }
        }
        $next();  // Proceed to controller
    }
}