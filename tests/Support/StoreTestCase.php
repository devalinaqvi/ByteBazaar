<?php
use PHPUnit\Framework\TestCase;
abstract class StoreTestCase extends TestCase
{
    protected PDO $db;
    protected function setUp(): void
    {
        $dsn = getenv("TEST_MYSQL_DSN") ?: "sqlite::memory:";
        if (
            $dsn !== "sqlite::memory:" &&
            !preg_match('/(?:^|;)dbname=byte_bazaar_test(?:;|$)/', $dsn)
        ) {
            throw new RuntimeException(
                "Only the disposable byte_bazaar_test database is allowed.",
            );
        }
        $this->db = new PDO(
            $dsn,
            $dsn === "sqlite::memory:" ? null : "root",
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        );
        if ($dsn === "sqlite::memory:") {
            $this->db->exec("PRAGMA foreign_keys = ON");
        } else {
            foreach (
                [
                    "order_items",
                    "orders",
                    "cart_items",
                    "products",
                    "categories",
                    "users",
                    "login_attempts",
                ]
                as $table
            ) {
                $this->db->exec("DROP TABLE IF EXISTS " . $table);
            }
        }
        migrate($this->db);
        $this->db->exec(
            "INSERT INTO categories (id,name,slug) VALUES (1,'Laptops','laptops')",
        );
        $s = $this->db->prepare(
            "INSERT INTO users (id,name,email,password,is_admin) VALUES (?,?,?,?,?)",
        );
        foreach (
            [
                [1, "Alice", "alice@example.test", 0],
                [2, "Bob", "bob@example.test", 0],
                [3, "Admin", "admin@example.test", 1],
            ]
            as [$id, $name, $email, $admin]
        ) {
            $s->execute([
                $id,
                $name,
                $email,
                password_hash("a-long-test-password", PASSWORD_DEFAULT),
                $admin,
            ]);
        }
        $this->db->exec(
            "INSERT INTO products (id,name,slug,description,price,stock,category_id) VALUES (1,'Laptop','laptop','A laptop',100.25,5,1),(2,'Desktop','desktop','A desktop',200.00,5,1)",
        );
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_SERVER["SCRIPT_NAME"] = "/index.php";
        $_SERVER["HTTP_ACCEPT"] = "application/json";
    }
    protected function services(array &$state): array
    {
        $session = new App\Core\SessionContext($state);
        $carts = new App\Repositories\CartRepository($this->db);
        $users = new App\Repositories\UserRepository($this->db);
        $orders = new App\Repositories\OrderRepository($this->db);
        $auth = new App\Services\AuthService($users, $session, $carts);
        return [
            "session" => $session,
            "cart" => new App\Services\CartService($carts, $session),
            "auth" => $auth,
            "orders" => new App\Services\OrderService($orders, $auth, $session),
            "checkout" => new App\Services\CheckoutService($orders, $session),
        ];
    }
    protected function address(string $key): array
    {
        return [
            "customer_name" => "Alice",
            "customer_email" => "alice@example.test",
            "shipping_address" => "123 Main Street",
            "shipping_city" => "Toronto",
            "shipping_country" => "Canada",
            "shipping_postal_code" => "A1A 1A1",
            "phone" => "5551234567",
            "payment_method" => "cash_on_delivery",
            "checkout_key" => $key,
        ];
    }
}
