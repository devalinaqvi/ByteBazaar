<?php
namespace App\Middleware;

use App\Core\Request;

class AdminMiddleware extends AuthMiddleware {
    public function handle(Request $request, callable $next): void {
        parent::handle($request, function() use ($next) {
            $user = $this->authService->getCurrentUser();
            if (!$user['is_admin']) {
                $this->redirect('/', ['error' => 'Access denied']);
            }
            $next();
        });
    }

    private function redirect(string $url, array $queryParams = []): void
    {
        $query = http_build_query($queryParams);
        header("Location: $url" . ($query ? "?$query" : ""), true, 302);
        exit;
    }
}