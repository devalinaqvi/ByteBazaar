<?php
namespace App\Middleware;

class AdminMiddleware extends AuthMiddleware {
    public function handle($request, \Closure $next): void {
        parent::handle($request, function() use ($next) {
            $user = $this->authService->getCurrentUser();
            if (!$user['is_admin']) {
                $this->redirect('/products', ['error' => 'Access denied']);
            }
            $next();
        });
    }
}