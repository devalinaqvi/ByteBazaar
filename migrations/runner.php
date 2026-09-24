<?php
function migrate(PDO $pdo): void
{
    $sqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === "sqlite";
    $schema = file_get_contents(__DIR__ . "/schema.sql");
    if ($sqlite) {
        $schema = str_replace(
            "PRIMARY KEY AUTO_INCREMENT",
            "PRIMARY KEY AUTOINCREMENT",
            $schema,
        );
    }
    foreach (explode(";", $schema) as $statement) {
        if (trim($statement)) {
            $pdo->exec($statement);
        }
    }
    $additions = [
        "products" => ["updated_at" => "TIMESTAMP NULL DEFAULT NULL"],
        "orders" => [
            "subtotal" => "DECIMAL(10,2) NULL",
            "shipping_amount" => "DECIMAL(10,2) NULL",
            "tax_amount" => "DECIMAL(10,2) NULL",
            "checkout_key" => "VARCHAR(64) NULL",
            "cart_owner" => "VARCHAR(255) NULL",
        ],
        "order_items" => ["product_name" => "VARCHAR(255) NULL"],
    ];
    foreach ($additions as $table => $columns) {
        $existing = $sqlite
            ? array_column(
                $pdo
                    ->query("PRAGMA table_info($table)")
                    ->fetchAll(PDO::FETCH_ASSOC),
                "name",
            )
            : array_column(
                $pdo
                    ->query("SHOW COLUMNS FROM $table")
                    ->fetchAll(PDO::FETCH_ASSOC),
                "Field",
            );
        foreach ($columns as $name => $definition) {
            if (!in_array($name, $existing, true)) {
                $pdo->exec("ALTER TABLE $table ADD COLUMN $name $definition");
            }
        }
    }
    $indexes = [
        "orders" => [
            "unique_checkout_key" => ["checkout_key", true],
            "idx_orders_user" => ["user_id", false],
        ],
        "products" => ["idx_products_category" => ["category_id", false]],
        "login_attempts" => ["idx_login_expiry" => ["expires_at", false]],
    ];
    foreach ($indexes as $table => $list) {
        $existing = $sqlite
            ? array_column(
                $pdo
                    ->query("PRAGMA index_list($table)")
                    ->fetchAll(PDO::FETCH_ASSOC),
                "name",
            )
            : array_column(
                $pdo
                    ->query("SHOW INDEX FROM $table")
                    ->fetchAll(PDO::FETCH_ASSOC),
                "Key_name",
            );
        foreach ($list as $name => [$column, $unique]) {
            if (!in_array($name, $existing, true)) {
                $pdo->exec(
                    "CREATE " .
                        ($unique ? "UNIQUE " : "") .
                        "INDEX $name ON $table ($column)",
                );
            }
        }
    }
    // Preserve historical orders if an account is ever deleted.
    if (!$sqlite) {
        $s = $pdo->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'user_id' AND REFERENCED_TABLE_NAME = 'users'",
        );
        foreach ($s->fetchAll(PDO::FETCH_COLUMN) as $name) {
            $safe = str_replace("`", "``", $name);
            $rule = $pdo->prepare(
                "SELECT DELETE_RULE FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ?",
            );
            $rule->execute([$name]);
            if ($rule->fetchColumn() !== "SET NULL") {
                $pdo->exec(
                    "ALTER TABLE orders DROP FOREIGN KEY `$safe`, ADD CONSTRAINT `fk_orders_user_preserved` FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",
                );
            }
        }
    }
}
