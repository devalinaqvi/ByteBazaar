<?php
namespace App\Middleware;

use App\Services\AuthService;  // For user checks

abstract class BaseMiddleware {
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    abstract public function handle($request, \Closure $next): void;  // $next = closure for chain

    protected function redirect(string $url, array $params = []): void {
        $query = http_build_query($params);
        header("Location: $url?" . ($query ? $query : ''));
        exit;
    }
}