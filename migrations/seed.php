<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$config = require __DIR__ . '/../config/config.php';
$db = new \App\Core\Database();
try {
    $pdo = $db->getConnection($config['db']);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Categories
$pdo->exec("INSERT INTO categories (name) VALUES ('Laptops'), ('Desktops'), ('Graphic Cards'), ('Memory')");

// Products (sample)
$products = [
    ['name' => 'Gaming Laptop', 'description' => 'High-end laptop', 'price' => 1299.99, 'image_url' => 'assets/images/laptop.jpg', 'category_id' => 1, 'stock' => 10],
    ['name' => 'Office Desktop', 'description' => 'Basic desktop PC', 'price' => 799.99, 'image_url' => 'assets/images/desktop.jpg', 'category_id' => 2, 'stock' => 5],
    // Add more...
];
foreach ($products as $p) {
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category_id, stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$p['name'], $p['description'], $p['price'], $p['image_url'], $p['category_id'], $p['stock']]);
}

// Admin user
$hashed = password_hash('password123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
$stmt->execute(['Admin User', 'thassan@algomau.ca', $hashed]);

echo "Seeded! Admin: thassan@algomau.ca / password123\n";