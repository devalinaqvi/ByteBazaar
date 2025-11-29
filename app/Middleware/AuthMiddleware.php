<?php
namespace App\Middleware;

use App\Services\AuthService;
use App\Core\Request;

class AuthMiddleware {
    protected AuthService $authService;
    private bool $guestOnly;

    public function __construct(AuthService $authService, bool $guestOnly = false) {
        $this->authService = $authService;
        $this->guestOnly = $guestOnly;
    }

    public function handle(Request $request, callable $next): void {
        $user = $this->authService->getCurrentUser();
        if ($this->guestOnly) {
            if ($user) {
                // Logged in? Skip to home
                header('Location: /', true, 302);
                exit;
            }
        } else {
            if (!$user) {
                // Not logged in? To login with return URL
                $redirect = $request->getUri();
                header("Location: /login?redirect=" . urlencode($redirect), true, 302);
                exit;
            }
        }
        $next();
    }
}