<?php
namespace App\Core;
final class Config
{
    public static function load(): array
    {
        $root = dirname(__DIR__, 2);
        $path = $root . "/config/config.php";
        $config = require is_file($path)
            ? $path
            : $root . "/config/config.example.php";
        if (!is_array($config) || !isset($config["db"])) {
            throw new \RuntimeException("Invalid database configuration");
        }
        foreach (
            [
                "DB_HOST" => "host",
                "DB_PORT" => "port",
                "DB_NAME" => "dbname",
                "DB_USER" => "user",
                "DB_PASSWORD" => "pass",
            ]
            as $env => $key
        ) {
            $value = getenv($env);
            if ($value !== false) {
                $config["db"][$key] = $value;
            }
        }
        return $config;
    }
    public static function sessionName(array $config, string $root): string
    {
        // Ports do not isolate browser cookies. Include DB/environment identity so
        // preview accounts cannot authenticate against another database on localhost.
        $identity = [
            realpath($root) ?: $root,
            getenv("APP_ENV") ?: "local",
            getenv("APP_DB_SQLITE") ?: "",
            $config["db"]["host"] ?? "",
            $config["db"]["port"] ?? 3306,
            $config["db"]["dbname"] ?? ($config["db"]["database"] ?? ""),
        ];
        return "byte_bazaar_" .
            substr(hash("sha256", json_encode($identity)), 0, 16);
    }
}
