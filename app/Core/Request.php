<?php
namespace App\Core;

class Request {
    public function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($_GET, $_POST, $_FILES);
    }

    public function file(string $key) {
        return $_FILES[$key] ?? null;
    }

    public function getUri(): string {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    }

    public function query(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}