<?php
namespace App\Core;

use AltoRouter;

class Router {
    private AltoRouter $altoRouter;
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->altoRouter = new AltoRouter();
        $this->altoRouter->setBasePath('');  // Empty for php -S root
    }

    public function get(string $path, string $target, string $name = ''): self {
        $this->altoRouter->map('GET', $path, $target, $name);
        return $this;
    }

    public function post(string $path, string $target, string $name = ''): self {
        $this->altoRouter->map('POST', $path, $target, $name);
        return $this;
    }

    // Chainable for routes/*.php: $router->get(...)->post(...)

    public function dispatch(string $uri, string $method): void {
        $cleanUri = parse_url($uri, PHP_URL_PATH);
        $cleanUri = $cleanUri === '/' ? '/' : rtrim($cleanUri, '/');

        $match = $this->altoRouter->match($cleanUri);

        if (!$match) {
            $this->handle404();
            return;
        }

        [$controllerName, $action] = explode('@', $match['target']);
        $controllerClass = "\\App\\Controllers\\{$controllerName}";

        $next = function() use ($controllerClass, $action, $match) {
            $this->callController($controllerClass, $action, $match['params']);
        };

        // Middleware (from earlier—inject authService if bound)
        $routeName = $match['name'] ?? '';
        $this->applyMiddleware($routeName, new Request(), $next);

        // Fallback if no middleware
        if (is_callable($next)) {
            $next();
        }
    }

    private function callController(string $controllerClass, string $action, array $params): void {
        try {
            $controller = $this->container->make($controllerClass);  // DI magic
            if (!method_exists($controller, $action)) {
                throw new \Exception("Method $action not in $controllerClass");
            }
            call_user_func_array([$controller, $action], $params);
        } catch (\Exception $e) {
            error_log("Controller error: " . $e->getMessage());
            $this->handle404();
        }
    }

// In applyMiddleware:
// In Router.php, replace the stub method:
    private function applyMiddleware(string $routeName, Request $request, callable $next): void {
        $authService = $this->container->get('authService');

        // Chain: Build nested closures (auth first, then admin)
        $middlewareChain = $next;

        if (str_starts_with($routeName, 'admin_')) {
            // Admin: Requires auth + is_admin
            $adminMiddleware = new \App\Middleware\AdminMiddleware($authService);
            $middlewareChain = function() use ($adminMiddleware, $request, $middlewareChain) {
                $adminMiddleware->handle($request, $middlewareChain);
            };
        } elseif (str_starts_with($routeName, 'protected_')) {
            // Protected: Auth only
            $authMiddleware = new \App\Middleware\AuthMiddleware($authService);
            $middlewareChain = function() use ($authMiddleware, $request, $middlewareChain) {
                $authMiddleware->handle($request, $middlewareChain);
            };
        } elseif (str_starts_with($routeName, 'guest_')) {
            // Guest: Redirect if logged in
            $guestMiddleware = new \App\Middleware\AuthMiddleware($authService, true);
            $middlewareChain = function() use ($guestMiddleware, $request, $middlewareChain) {
                $guestMiddleware->handle($request, $middlewareChain);
            };
        }

        // Execute chain
        $middlewareChain();
    }

    private function handle404(): void {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        exit;
    }
}