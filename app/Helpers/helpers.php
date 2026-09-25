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

if (!function_exists('product_description_templates')) {

    /**
     * Return product description templates.
     * Supports general templates, laptop templates, and GPU templates.
     */
    function product_description_templates(string $category = 'general'): array
    {
        // Normalize category
        $key = strtolower(trim($category));

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
            'graphics cards' => $gpus,
            'graphic cards' => $gpus,
        ];

        $selected = $collections[$key] ?? $general;

        return array_map('clean_template', $selected);
    }
}

function generate_product_description(string $category = 'general'): array
{
    $templates = product_description_templates($category);
    $templateId = random_int(0, count($templates) - 1);
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

    return [
        'template_id' => $templateId,
        'template' => $templates[$templateId],
        'tokens' => $tokens,
    ];
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
     * Replaces template tokens as plain text. Escape with e() when rendering HTML.
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

        // Accept plain scalar values only.
        $values = array_map(static fn ($value) => is_scalar($value) ? (string) $value : '', $values);

        // Replace tokens
        $final = str_replace($tokens, $values, $template);

        // Remove leftover tokens safely (future tokens, if not used)
        $final = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $final);

        // Clean extra spaces
        return preg_replace('/\s+/', ' ', trim($final));
    }
}
