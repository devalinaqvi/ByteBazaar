<?php
/**
 * Robust helpers for filesystem paths and URLs.
 * Put this in app/Helpers/helpers.php and require it from public/index.php
 */

/**
 * Returns absolute project base path (filesystem).
 * Example: base_path('app/Controllers') => /var/www/project-root/app/Controllers
 */
function base_path(string $path = ''): string
{
    // dirname(__DIR__, 2) returns project-root when this file is in app/Helpers/
    $base = rtrim(dirname(__DIR__, 2), DIRECTORY_SEPARATOR);
    return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

/**
 * Returns public path (filesystem).
 * Example: public_path('uploads') => /var/www/project-root/public/uploads
 */
function public_path(string $path = ''): string
{
    $p = base_path('public');
    return $path === '' ? $p : $p . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

/**
 * Returns the request scheme (http or https) in a safe way.
 * Falls back to 'http' if nothing else is available.
 */
function request_scheme(): string
{
    // CLI or missing server variables -> default to http
    if (php_sapi_name() === 'cli' || empty($_SERVER)) {
        return 'http';
    }

    // Preferred: use SERVER_PROTOCOL or X-Forwarded-Proto if behind proxy (trusting proxy requires config)
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        // If using a trusted reverse proxy, this header may be present
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO']);
        return trim($parts[0]);
    }

    if (!empty($_SERVER['REQUEST_SCHEME'])) {
        return $_SERVER['REQUEST_SCHEME'];
    }

    // Typical alternative check:
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return 'https';
    }

    // Some servers (IIS) use SERVER_PORT
    if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        return 'https';
    }

    return 'http';
}

/**
 * Returns base URL like "https://example.com" or "http://localhost:8000"
 * Optionally supply path to append.
 *
 * NOTE: If you are behind proxies you may want to adjust to trust X-Forwarded-Host.
 */
function base_url(string $path = ''): string
{
    // If running via CLI (tests), return localhost
    if (php_sapi_name() === 'cli' || empty($_SERVER)) {
        $host = 'localhost';
        $port = (isset($_SERVER['SERVER_PORT']) ? ':' . $_SERVER['SERVER_PORT'] : '');
        $url = request_scheme() . '://' . $host . $port;
        return $path ? rtrim($url, '/') . '/' . ltrim($path, '/') : rtrim($url, '/');
    }

    // Host detection
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

    // Build base (preserve port if present in HTTP_HOST)
    $scheme = request_scheme();
    $url = $scheme . '://' . $host;

    return $path ? rtrim($url, '/') . '/' . ltrim($path, '/') : rtrim($url, '/');
}

/**
 * Returns public asset URL (points to public/assets/*).
 * Example: asset('css/app.css') => https://example.com/assets/css/app.css
 */
function asset(string $path): string
{
    // If you host under a subdirectory, set base path in config and read it here.
    // For now we assume public is root.
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Helper to escape output (very small HTML-escaping helper).
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Helper to write custom log messages.
 */
function logMessage(string $message): void
{
    $file = base_path('storage/logs/app.log');
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message" . PHP_EOL, 3, $file);
}

function view(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require base_path('app/Views/' . $view . '.php');
    return ob_get_clean();
}