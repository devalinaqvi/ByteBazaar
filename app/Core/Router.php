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

        $e = null;
        try {
            $match = $this->altoRouter->match($cleanUri);
        }
        catch (\Exception $e) {
            $this->handle404($e);
            return;
        }

        if (!$match) {
            logMessage("No route found for $method $uri");
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
            try {
                $controller = $this->container->get($controllerClass);
            } catch (\Exception $e) {
                // Fallback: Check if the controller has dependencies
                $reflection = new \ReflectionClass($controllerClass);
                $constructor = $reflection->getConstructor();

                if ($constructor && $constructor->getNumberOfRequiredParameters() > 0) {
                    throw new \Exception("Controller $controllerClass requires binding in container");
                }
                $controller = new $controllerClass();
            }
            if (!method_exists($controller, $action)) {
                throw new \Exception("Method $action not in $controllerClass");
            }
            // Convert associative params to positional values only
            $positionalParams = array_values($params);

            call_user_func_array([$controller, $action], $positionalParams);
        } catch (\Exception $e) {
            error_log("Controller error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->handle404($e);
        }
    }

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

    private function handle404($e): void {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>' . ($e ? "<p>$e</p>" : '');
        error_log("404 error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        exit;
    }
}