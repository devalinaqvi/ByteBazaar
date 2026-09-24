<?php
if (PHP_SAPI !== "cli") {
    http_response_code(404);
    exit();
}
require dirname(__DIR__) . "/vendor/autoload.php";
$config = App\Core\Config::load();
$pdo = App\Core\Database::getConnection($config["db"]);
require __DIR__ . "/runner.php";
migrate($pdo);
$categories = [
    "Laptops",
    "Desktops",
    "Graphics cards",
    "Headphones",
    "Mobiles",
];
$ids = [];
foreach ($categories as $name) {
    $slug = str_replace(" ", "-", strtolower($name));
    $s = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $s->execute([$slug]);
    $id = $s->fetchColumn();
    if (!$id) {
        $s = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $s->execute([$name, $slug]);
        $id = $pdo->lastInsertId();
    }
    $ids[$name] = (int) $id;
}
$products = [
    [
        "StudioBook Pro 14",
        "Laptops",
        "A compact laptop for focused work and creative projects. 16 GB memory, 512 GB storage, and a crisp 14-inch display.",
        "1299.00",
        12,
        "laptops.jpg",
    ],
    [
        "Everyday Notebook 15",
        "Laptops",
        "Room to work, study, and unwind. A practical everyday notebook with 8 GB memory and 256 GB storage.",
        "699.00",
        20,
        "laptops.jpg",
    ],
    [
        "Creator Desktop",
        "Desktops",
        "A capable desktop for your creative workspace. 32 GB memory, 1 TB storage, and dedicated graphics.",
        "1699.00",
        8,
        "desktops.jpg",
    ],
    [
        "GeForce RTX Graphics",
        "Graphics cards",
        "Upgrade your desktop for immersive gaming and accelerated creative work. Check your case and power supply compatibility before ordering.",
        "549.00",
        10,
        "graphics-card.webp",
    ],
    [
        "Studio Wireless",
        "Headphones",
        "Over-ear wireless headphones for your daily soundtrack, with a comfortable fit and a built-in microphone.",
        "149.00",
        25,
        "headphones.webp",
    ],
    [
        "Everyday Smartphone",
        "Mobiles",
        "Stay connected with a bright display, 128 GB storage, and a versatile camera system.",
        "459.00",
        18,
        "mobiles.png",
    ],
];
foreach (
    $products
    as [$name, $category, $description, $price, $stock, $image]
) {
    $slug = strtolower(str_replace(" ", "-", $name));
    $s = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
    $s->execute([$slug]);
    if (!$s->fetchColumn()) {
        $s = $pdo->prepare(
            "INSERT INTO products (name, slug, description, price, stock, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)",
        );
        $s->execute([
            $name,
            $slug,
            $description,
            $price,
            $stock,
            $ids[$category],
            "/assets/images/categories/" . $image,
        ]);
    }
}
$email = getenv("SEED_ADMIN_EMAIL");
$password = getenv("SEED_ADMIN_PASSWORD");
if ($email || $password) {
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !$password ||
        strlen($password) < 12 ||
        strlen($password) > 72
    ) {
        throw new RuntimeException(
            "Provide a valid SEED_ADMIN_EMAIL and a 12–72 character SEED_ADMIN_PASSWORD.",
        );
    }
    $s = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $s->execute([strtolower($email)]);
    if (!$s->fetchColumn()) {
        $s = $pdo->prepare(
            "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)",
        );
        $s->execute([
            "Store administrator",
            strtolower($email),
            password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
echo "Demo catalog seeded. Existing accounts and products were preserved.\n";
