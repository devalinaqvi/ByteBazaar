<?php
namespace App\Core;

use PDO;
use Exception;

class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(array $config): PDO {
        if (!is_array($config)) {
            throw new Exception('DB config must be array');
        }

        if (empty($config['host']) || empty($config['dbname'])) {
            throw new Exception('Missing DB host/dbname in config');
        }

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=" . ($config['charset'] ?? 'utf8mb4');
        self::$pdo = new PDO($dsn, $config['user'] ?? '', $config['pass'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        return self::$pdo;
    }
}