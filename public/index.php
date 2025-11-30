<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();  // Early

error_log('URI: ' . $_SERVER['REQUEST_URI']);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Helpers/helpers.php';
require_once __DIR__ . '/../app/Core/Database.php';

// Load config once (idempotent: require returns array or null)
$configFile = base_path('config/config.php');
if (!file_exists($configFile)) {
    die('Missing config.php—create it with DB creds!');
}
$config = require $configFile;  // Captures return

// Null guard and fallback (dev only—remove for prod)
if (!is_array($config) || !isset($config['db'])) {
    error_log('Config invalid—using fallback');
    $config = [
        'db' => [
            'host' => 'localhost',
            'dbname' => 'byte_bazaar',  // Your DB name
            'user' => 'devali',
            'pass' => 'devali'  // XAMPP/LAMP default
        ]
    ];
}

// Gen CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Init DB
try {
    $pdo = \App\Core\Database::getConnection($config['db']);
} catch (\TypeError $e) {
    die('DB Config Error: ' . $e->getMessage() . '—Check config.php array.');
} catch (\PDOException $e) {
    die('DB Connection Error: ' . $e->getMessage());
}

// DI Container (pass PDO)
$container = new \App\Core\Container($pdo);

// Bind deps
$container->singleton('request', fn($c) => new \App\Core\Request());
$container->singleton('cartRepo', fn($c) => new \App\Repositories\CartRepository($c->get('pdo')));
$container->singleton('cartService', fn($c) => new \App\Services\CartService($c->get('cartRepo')));
$container->singleton('userRepository', fn($c) => new \App\Repositories\UserRepository($c->get('db')));
$container->singleton('orderRepo', fn($c) => new \App\Repositories\OrderRepository($c->get('db')));
$container->singleton('orderService', fn($c) => new \App\Services\OrderService($c->get('orderRepo')));

try {
    $userRepo = $container->get('userRepository');
    error_log('userRepository OK');
} catch (\Exception $e) {
    error_log('userRepository FAILED: ' . $e->getMessage());
}
$container->singleton('authService', fn($c) => new \App\Services\AuthService($c->get('userRepository')));

// Bind controllers
$container->bind('\\App\\Controllers\\HomeController', fn($c) => new \App\Controllers\HomeController());
$container->bind('\\App\\Controllers\\CartController', fn($c) => new \App\Controllers\CartController($c->get('cartService')));
$container->bind('\\App\\Controllers\\AuthController', fn($c) => new \App\Controllers\AuthController($c->get('authService'), $c->get('request')));
$container->bind('\\App\\Controllers\\AdminController', fn($c) => new \App\Controllers\AdminController($c->get('orderService')));

$router = new \App\Core\Router($container);

// Load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>