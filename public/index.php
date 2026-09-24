<?php
error_reporting(E_ALL);
ini_set("display_errors", "0");
ini_set("session.use_strict_mode", "1");
ini_set("session.use_only_cookies", "1");
require dirname(__DIR__) . "/vendor/autoload.php";
require dirname(__DIR__) . "/app/Helpers/helpers.php";
try {
    $config = App\Core\Config::load();
    session_name(App\Core\Config::sessionName($config, dirname(__DIR__)));
    $cookiePath = rtrim(
        str_replace(
            "\\",
            "/",
            dirname($_SERVER["SCRIPT_NAME"] ?? "/index.php"),
        ),
        "/",
    );
    session_set_cookie_params([
        "lifetime" => 0,
        "path" => ($cookiePath === "." ? "" : $cookiePath) . "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
    session_start();
    header("Cache-Control: private, no-store");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header(
        "Content-Security-Policy: default-src 'self'; img-src 'self' data: blob: https:; style-src 'self'; script-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'",
    );
    $pdo = App\Core\Database::getConnection($config["db"]);
    $container = require base_path("app/bootstrap.php");
    $router = new App\Core\Router($container);
    require base_path("routes/web.php");
    require base_path("routes/admin.php");
    require base_path("routes/user.php");
    $router->dispatch($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);
} catch (Throwable $e) {
    error_log("Application startup failed: " . $e->getMessage());
    http_response_code(503);
    if (wants_json()) {
        header("Content-Type: application/json");
        echo json_encode([
            "success" => false,
            "message" =>
                "The store is temporarily unavailable. Please try again shortly.",
        ]);
    } else {
        $title = "Back shortly";
        $view = "pages/error";
        $error =
            "The store is temporarily unavailable. Please try again shortly.";
        require base_path("app/Views/layouts/main.php");
    }
}
