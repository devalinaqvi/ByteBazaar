<?php
// Disposable database only. Requires pcntl and a local MySQL instance.
require dirname(__DIR__) . "/vendor/autoload.php";
require dirname(__DIR__) . "/app/Helpers/helpers.php";
require dirname(__DIR__) . "/migrations/runner.php";
$dsn = getenv("TEST_MYSQL_DSN");
if (
    !$dsn ||
    !preg_match('/(?:^|;)dbname=byte_bazaar_test_concurrency(?:;|$)/', $dsn) ||
    !function_exists("pcntl_fork")
) {
    throw new RuntimeException(
        "Use the disposable byte_bazaar_test_concurrency database and enable pcntl.",
    );
}
function connection(): PDO
{
    return new PDO(getenv("TEST_MYSQL_DSN"), "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
function setupFixture(): void
{
    $db = connection();
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
        $db->exec("DROP TABLE IF EXISTS " . $table);
    }
    migrate($db);
    $db->exec(
        "INSERT INTO users (id,name,email,password) VALUES (1,'One','one@example.test','unused'),(2,'Two','two@example.test','unused')",
    );
    $db->exec(
        "INSERT INTO products (id,name,slug,description,price,stock) VALUES (1,'Last laptop','last-laptop','Test',100.00,1)",
    );
}
function race(array $workers): array
{
    $folder = sys_get_temp_dir() . "/byte-race-" . bin2hex(random_bytes(6));
    mkdir($folder, 0700);
    $children = [];
    foreach ($workers as $index => [$user, $key]) {
        $pid = pcntl_fork();
        if ($pid === -1) {
            throw new RuntimeException("Unable to fork");
        }
        if ($pid === 0) {
            file_put_contents("$folder/ready-$index", "1");
            while (!is_file("$folder/go")) {
                usleep(1000);
            }
            try {
                $repo = new App\Repositories\OrderRepository(connection());
                $id = $repo->place(
                    "user:" . $user,
                    $user,
                    [
                        "customer_name" => "Test",
                        "customer_email" => "test@example.test",
                        "shipping_address" => "123 Test",
                        "shipping_city" => "Test",
                        "shipping_country" => "Canada",
                        "shipping_postal_code" => "A1A1A1",
                        "phone" => "5550100",
                        "payment_method" => "cash_on_delivery",
                    ],
                    $key,
                );
                $result = ["id" => $id];
            } catch (App\Core\HttpException $e) {
                $result = ["status" => $e->status];
            } catch (Throwable $e) {
                $result = ["error" => $e->getMessage()];
            }
            file_put_contents("$folder/result-$index", json_encode($result));
            exit();
        }
        $children[] = $pid;
    }
    $deadline = microtime(true) + 5;
    while (count(glob("$folder/ready-*")) < count($workers)) {
        if (microtime(true) > $deadline) {
            throw new RuntimeException("Worker startup timeout");
        }
        usleep(1000);
    }
    touch("$folder/go");
    foreach ($children as $pid) {
        pcntl_waitpid($pid, $status);
    }
    $results = [];
    foreach (array_keys($workers) as $index) {
        $results[] = json_decode(
            file_get_contents("$folder/result-$index"),
            true,
        );
    }
    foreach (glob("$folder/*") as $file) {
        unlink($file);
    }
    rmdir($folder);
    return $results;
}
setupFixture();
$db = connection();
$db->exec(
    "INSERT INTO cart_items(session_id,product_id,quantity) VALUES ('user:1',1,1),('user:2',1,1)",
);
$db = null;
$results = race([[1, str_repeat("a", 64)], [2, str_repeat("b", 64)]]);
$db = connection();
if (
    count(array_filter($results, fn($r) => isset($r["id"]))) !== 1 ||
    count(array_filter($results, fn($r) => ($r["status"] ?? null) === 409)) !==
        1 ||
    (int) $db->query("SELECT stock FROM products")->fetchColumn() !== 0
) {
    throw new RuntimeException(
        "Oversell race failed: " . json_encode($results),
    );
}
echo "PASS: Two customers competing for the final unit produce one order and one stock conflict.\n";
$db = null;
setupFixture();
$db = connection();
$db->exec(
    "INSERT INTO cart_items(session_id,product_id,quantity) VALUES ('user:1',1,1)",
);
$db = null;
$results = race([[1, str_repeat("c", 64)], [1, str_repeat("c", 64)]]);
$db = connection();
if (
    !isset($results[0]["id"], $results[1]["id"]) ||
    $results[0]["id"] !== $results[1]["id"] ||
    (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn() !== 1
) {
    throw new RuntimeException(
        "Idempotency race failed: " . json_encode($results),
    );
}
echo "PASS: Concurrent retries return the same order and decrement stock once.\n";
// Verify the additive migration against the original schema, without original data.
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
    $db->exec("DROP TABLE IF EXISTS " . $table);
}
foreach (
    explode(";", file_get_contents(__DIR__ . "/Support/legacy_schema.sql"))
    as $sql
) {
    if (trim($sql)) {
        $db->exec($sql);
    }
}
migrate($db);
migrate($db);
echo "PASS: Original MySQL schema upgrades successfully and the migration can be rerun.\n";
