<?php
use App\Core\{Container, Request, SessionContext};
use App\Repositories\{
    CartRepository,
    UserRepository,
    OrderRepository,
    ProductRepository,
    CategoryRepository,
};
use App\Services\{
    CartService,
    AuthService,
    OrderService,
    CheckoutService,
    ProductService,
    CategoryService,
    UserService,
    FileUploadService,
    LoginLimiter,
};

// A request owns its container and SessionContext; nothing is process-global.
$container = new Container($pdo);
$container->singleton("session", new SessionContext($_SESSION));
$container->singleton("request", fn() => new Request());
$container->singleton("cartRepo", fn($c) => new CartRepository($c->get("pdo")));
$container->singleton(
    "userRepository",
    fn($c) => new UserRepository($c->get("pdo")),
);
$container->singleton(
    "orderRepo",
    fn($c) => new OrderRepository($c->get("pdo")),
);
$container->singleton(
    "productRepo",
    fn($c) => new ProductRepository($c->get("pdo")),
);
$container->singleton(
    "categoryRepo",
    fn($c) => new CategoryRepository($c->get("pdo")),
);
$container->singleton(
    "authService",
    fn($c) => new AuthService(
        $c->get("userRepository"),
        $c->get("session"),
        $c->get("cartRepo"),
    ),
);
$container->singleton(
    "cartService",
    fn($c) => new CartService($c->get("cartRepo"), $c->get("session")),
);
$container->singleton(
    "orderService",
    fn($c) => new OrderService(
        $c->get("orderRepo"),
        $c->get("authService"),
        $c->get("session"),
    ),
);
$container->singleton(
    "checkoutService",
    fn($c) => new CheckoutService($c->get("orderRepo"), $c->get("session")),
);
$container->singleton(
    "categoryService",
    fn($c) => new CategoryService($c->get("categoryRepo")),
);
$container->singleton("fileUploadService", fn() => new FileUploadService());
$container->singleton(
    "productService",
    fn($c) => new ProductService(
        $c->get("productRepo"),
        $c->get("fileUploadService"),
        $c->get("categoryService"),
    ),
);
$container->singleton(
    "userService",
    fn($c) => new UserService($c->get("userRepository")),
);
$container->singleton(
    "loginLimiter",
    fn($c) => new LoginLimiter($c->get("pdo")),
);
$container->bind(
    "\\App\\Controllers\\HomeController",
    fn($c) => new App\Controllers\HomeController(
        $c->get("categoryService"),
        $c->get("productService"),
    ),
);
$container->bind(
    "\\App\\Controllers\\ProductController",
    fn($c) => new App\Controllers\ProductController(
        $c->get("productService"),
        $c->get("request"),
    ),
);
$container->bind(
    "\\App\\Controllers\\CartController",
    fn($c) => new App\Controllers\CartController(
        $c->get("cartService"),
        $c->get("request"),
    ),
);
$container->bind(
    "\\App\\Controllers\\AuthController",
    fn($c) => new App\Controllers\AuthController(
        $c->get("authService"),
        $c->get("request"),
        $c->get("loginLimiter"),
    ),
);
$container->bind(
    "\\App\\Controllers\\OrderController",
    fn($c) => new App\Controllers\OrderController(
        $c->get("orderService"),
        $c->get("checkoutService"),
        $c->get("cartService"),
        $c->get("request"),
        $c->get("session"),
        $c->get("authService"),
    ),
);
$container->bind(
    "\\App\\Controllers\\AdminController",
    fn($c) => new App\Controllers\AdminController(
        $c->get("orderService"),
        $c->get("productService"),
        $c->get("categoryService"),
        $c->get("userService"),
        $c->get("request"),
    ),
);
$container->bind(
    "\\App\\Controllers\\CategoryController",
    fn($c) => new App\Controllers\CategoryController(
        $c->get("categoryService"),
        $c->get("request"),
    ),
);
$container->bind(
    "\\App\\Controllers\\UserController",
    fn($c) => new App\Controllers\UserController(
        $c->get("orderService"),
        $c->get("request"),
        $c->get("session"),
        $c->get("authService"),
    ),
);
$container->singleton(
    "view",
    fn($c) => new App\Core\ViewRenderer(
        $c->get("authService"),
        $c->get("cartService"),
    ),
);
return $container;
