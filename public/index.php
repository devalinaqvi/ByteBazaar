<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();  // Early

error_log('URI: ' . $_SERVER['REQUEST_URI']);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Database.php';

// Load config once (idempotent: require returns array or null)
$configFile = __DIR__ . '/../config/config.php';
if (!file_exists($configFile)) {
    die('Missing config.php—create it with DB creds!');
}
$config = require $configFile;  // Captures return

// Null guard + fallback (dev only—remove for prod)
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
$container->singleton('cartRepo', fn($c) => new \App\Repositories\CartRepository($c->get('pdo')));
$container->singleton('cartService', fn($c) => new \App\Services\CartService($c->get('cartRepo')));

// Bind controllers
$container->bind('App\Controllers\CartController', fn($c) => new \App\Controllers\CartController($c->get('cartService')));

$router = new \App\Core\Router($container);

// Load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>