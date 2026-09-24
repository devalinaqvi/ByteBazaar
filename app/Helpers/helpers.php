<?php
function base_path(string $path = ""): string
{
    $root = dirname(__DIR__, 2);
    return $path === "" ? $root : $root . "/" . ltrim($path, "/");
}
function public_path(string $path = ""): string
{
    return base_path("public" . ($path === "" ? "" : "/" . ltrim($path, "/")));
}
function url_path(string $path = ""): string
{
    $base = str_replace(
        "\\",
        "/",
        dirname($_SERVER["SCRIPT_NAME"] ?? "/index.php"),
    );
    $base = preg_replace('#/public$#', "", $base);
    if ($base === "/" || $base === ".") {
        $base = "";
    }
    return $path === "" ? $base : rtrim($base, "/") . "/" . ltrim($path, "/");
}
function asset(string $path): string
{
    return url_path("assets/" . ltrim($path, "/"));
}
function upload_url(?string $path): string
{
    if (!$path) {
        return "";
    }
    if (preg_match("#^https?://#", $path)) {
        return $path;
    }
    $file = realpath(public_path(ltrim($path, "/")));
    $root = realpath(public_path()) . DIRECTORY_SEPARATOR;
    if (!$file || !str_starts_with($file, $root) || !is_file($file)) {
        return "";
    }
    return url_path(ltrim($path, "/"));
}
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8",
    );
}
function csrf_hash(): string
{
    return $_SESSION["csrf_token"] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        e(csrf_hash()) .
        '">';
}
function wants_json(): bool
{
    return str_contains($_SERVER["HTTP_ACCEPT"] ?? "", "application/json") ||
        strtolower($_SERVER["HTTP_X_REQUESTED_WITH"] ?? "") ===
            "xmlhttprequest";
}
function money(int|float|string $amount): string
{
    return '$' . number_format((float) $amount, 2);
}
function mapCategoryImage(string $category): string
{
    $category = strtolower($category);
    $category =
        [
            "graphics cards" => "graphics-card",
            "graphic cards" => "graphics-card",
            "graphics card" => "graphics-card",
            "phones" => "mobiles",
        ][$category] ?? $category;
    foreach (["jpg", "png", "webp"] as $extension) {
        $path = "images/categories/" . $category . "." . $extension;
        if (is_file(public_path("assets/" . $path))) {
            return asset($path);
        }
    }
    return asset("images/categories/default.webp");
}
