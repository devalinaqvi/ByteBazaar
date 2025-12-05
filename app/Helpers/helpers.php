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
 * Returns a public path (filesystem).
 * Example: public_path('uploads') => /var/www/project-root/public/uploads
 */
function public_path(string $path = ''): string
{
    $p = base_path('public');
    return $path === '' ? $p : $p . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

/**
 * Returns the request scheme (http or https) safely.
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
    //Check if we are running Apache or Nginx behind a proxy and adjust the scheme accordingly
    $isApache = str_contains($_SERVER['SERVER_SOFTWARE'] ?? '', 'Apache');
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
/**
 * Helper to create route urls
 */
function route(string $route, array $params = []): string
{
    $url = base_url($route);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

function csrf_hash(): string
{
    return hash('sha256', session_id() . $_SERVER['REMOTE_ADDR'] . time());
}

function mapCategoryImage(string $category): string
{
    $category = strtolower($category);

    $possibleFiles = [
        $category . '.jpg',
        $category . '.png',
        $category . '.webp',
    ];

    foreach ($possibleFiles as $filename) {
        // Use public_path() to get the correct filesystem path
        $filePath = public_path('assets/images/categories/' . $filename);

        if (file_exists($filePath)) {
            // Return the URL using asset() helper
            return asset('images/categories/' . $filename);
        }
    }

    // Return default image URL
    return asset('images/categories/default.webp');
}

function returnBannerImage(string $banner): string
{
    $banner = strtolower($banner);

    $possibleFiles = [
        $banner . '.jpg',
        $banner . '.png',
        $banner . '.webp',
    ];

    foreach ($possibleFiles as $filename) {
        // Use public_path() to get the correct filesystem path
        $filePath = public_path('assets/images/banner/' . $filename);

        if (file_exists($filePath)) {
            // Return the URL using asset() helper
            return asset('images/banner/' . $filename);
        }
    }

    // Return default image URL
    return asset('images/banner/default.webp');
}


if (!function_exists('generate_product_description')) {

    /**
     * Generate a random product description template.
     * Supports general templates, laptop templates, and GPU templates.
     */
    function generate_product_description(string $category = 'general'): array
    {
        // Normalize category
        $key = strtolower($category);

        // Token options (replace later)
        $tokens = [
            '{name}',
            '{brand}',
            '{category}',
            '{price}',
            '{specs}',
            '{cpu}',
            '{gpu}',
            '{ram}',
            '{storage}',
            '{display}',
            '{feature_1}',
            '{feature_2}',
            '{feature_3}'
        ];

        // GENERAL TEMPLATES (0–9)
        $general = [
            "The {name} is engineered for exceptional performance, offering unmatched value in the {category} category. Designed for professionals and casual users alike.",
            "{name} delivers powerful performance and everyday reliability. Its advanced {specs} make it a smart upgrade for any user.",
            "Experience smooth performance with {name}, featuring modern technology and durable build quality designed to last.",
            "{name} combines speed, efficiency, and affordability. Ideal for multitasking, gaming, or productivity at a competitive price of {price}.",
            "Built with precision, {name} provides seamless performance powered by advanced components. Perfect for users seeking reliability.",
            "Enjoy the best of power and portability with {name}. It offers excellent value for its feature set, making it a great choice in the {category} range.",
            "If you're looking for speed and reliability, {name} is built for you. Its {specs} configuration ensures top-tier results.",
            "The {name} provides a perfect balance of performance and efficiency, delivering smooth operation for all types of workloads.",
            "With premium build quality and enhanced speed, {name} stands out as a powerful option in the {category} category.",
            "Unlock better productivity with {name}. Its optimized performance and advanced features ensure dependable daily use.",
        ];

        // LAPTOP TEMPLATES (10–19)
        $laptops = [
            "{name} offers an exceptional laptop experience with its powerful {cpu}, responsive {display}, and long-lasting battery life.",
            "Designed for productivity and mobility, {name} features {ram} RAM, {storage} storage, and a crisp {display}.",
            "Take your work anywhere with {name}, built with modern hardware including {cpu} and high-performance {gpu}.",
            "{name} delivers premium portability and all-day battery life, making it perfect for students, professionals, and creators.",
            "Experience superior laptop performance with {name}, equipped with {ram} RAM and fast {storage} for smooth multitasking.",
            "The {brand} {name} offers incredible display clarity, powerful processing, and lightweight design ideal for travel.",
            "{name} combines performance and durability, featuring advanced cooling, sleek aesthetics, and dependable power.",
            "Enjoy efficient computing with {name}, built with a responsive {display}, high-speed storage, and modern {cpu}.",
            "{name} is designed for both work and entertainment, delivering smooth visuals and reliable performance.",
            "{name} offers a complete laptop experience with stunning graphics, improved thermals, and productivity-focused features.",
        ];

        // GPU TEMPLATES (20–29)
        $gpus = [
            "{name} offers outstanding gaming performance with its advanced {gpu} architecture and improved power efficiency.",
            "Boost your gaming and rendering tasks with {name}, delivering ultra-smooth frame rates and enhanced ray-tracing capabilities.",
            "{brand}'s {name} is built for creators and gamers demanding top-tier GPU performance in modern applications.",
            "{name} features high-speed memory, improved cooling systems, and powerful graphics output suitable for AAA gaming.",
            "With cutting-edge architecture, {name} brings next-level performance for demanding workloads and real-time rendering.",
            "Designed for enthusiasts, {name} offers exceptional overclocking potential and stable high-performance output.",
            "{name} delivers immersive visuals, smooth gameplay, and future-ready performance in the GPU market.",
            "Achieve maximum performance in productivity and gaming with {name}, equipped with enhanced cooling and optimized power draw.",
            "The {name} GPU provides strong rasterization, ray tracing, and AI-accelerated performance for modern software.",
            "{name} is engineered with premium components to deliver consistent frame rates and exceptional visual quality.",
        ];

        // Select correct group
        $collections = [
            'general' => $general,
            'laptop' => $laptops,
            'laptops' => $laptops,
            'gpu' => $gpus,
            'gpus' => $gpus,
            'graphics card' => $gpus,
            'graphic card' => $gpus,
        ];

        $selected = $collections[$key] ?? $general;

        // Pick random template
        $templateId = random_int(0, count($selected) - 1);
        $template = clean_template($selected[$templateId]);

        return [
            'template_id' => $templateId,
            'template' => $template,
            'tokens' => $tokens
        ];
    }
}

/**
 * Removes unnecessary whitespace & newlines.
 */
if (!function_exists('clean_template')) {
    function clean_template(string $text): string
    {
        return trim(
            preg_replace('/\s+/', ' ', $text)
        );
    }
}

if (!function_exists('render_description')) {

    /**
     * Replaces template tokens safely.
     * Fields missing in $data will be replaced with empty string.
     */
    function render_description(string $template, array $data): string
    {
        $tokens = [
            '{name}', '{brand}', '{price}', '{cpu}', '{ram}',
            '{storage}', '{display}', '{category}', '{gpu}', '{specs}',
            '{feature_1}', '{feature_2}', '{feature_3}'
        ];

        // Build replacement array (use fallback if missing)
        $values = [
            $data['name']        ?? '',
            $data['brand']       ?? '',
            $data['price']       ?? '',
            $data['cpu']         ?? '',
            $data['ram']         ?? '',
            $data['storage']     ?? '',
            $data['display']     ?? '',
            $data['category']    ?? ($data['category_name'] ?? ''),
            $data['gpu']         ?? '',
            $data['specs']       ?? '',
            $data['feature_1']   ?? '',
            $data['feature_2']   ?? '',
            $data['feature_3']   ?? '',
        ];

        // Replace tokens
        $final = str_replace($tokens, $values, $template);

        // Remove leftover tokens safely (future tokens, if not used)
        $final = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $final);

        // Clean extra spaces
        return preg_replace('/\s+/', ' ', trim($final));
    }
}

