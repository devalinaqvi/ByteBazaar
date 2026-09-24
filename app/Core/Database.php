<?php
namespace App\Core;
use PDO;
final class Database
{
    public static function getConnection(array $config): PDO
    {
        $sqlite = getenv("APP_DB_SQLITE");
        $dsn = $sqlite
            ? "sqlite:" . $sqlite
            : "mysql:host=" .
                ($config["host"] ?? "localhost") .
                ";port=" .
                ($config["port"] ?? 3306) .
                ";dbname=" .
                ($config["dbname"] ?? ($config["database"] ?? "")) .
                ";charset=utf8mb4";
        $pdo = new PDO(
            $dsn,
            $config["user"] ?? "",
            $config["pass"] ?? ($config["password"] ?? ""),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
        if ($sqlite) {
            $pdo->exec("PRAGMA foreign_keys = ON");
            $pdo->exec("PRAGMA busy_timeout = 5000");
        }
        return $pdo;
    }
}
