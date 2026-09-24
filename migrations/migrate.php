<?php
// Non-destructive, rerunnable additions for the original schema and clean installs.
if (PHP_SAPI !== "cli") {
    http_response_code(404);
    exit();
}
require dirname(__DIR__) . "/vendor/autoload.php";
$config = App\Core\Config::load();
$pdo = App\Core\Database::getConnection($config["db"]);
require __DIR__ . "/runner.php";
migrate($pdo);
echo "Database schema is up to date.\n";
