<?php
namespace App\Core;

class BaseController {
    private static bool $rendered = false;  // Prevent dupe renders (per request)

    protected function redirect(string $url, array $params = []): void {
        $query = http_build_query($params);
        header("Location: {$url}" . ($query ? "?{$query}" : ''), true, 302);
        exit;
    }

    protected function render(string $view, array $data = []): void {
        if (static::$rendered) {
            error_log('Render skipped: already rendered once');  // Log dupe
            return;
        }
        static::$rendered = true;

        // Shared data
        $data['csrf_token'] = $_SESSION['csrf_token'] ?? '';
        $data['is_logged_in'] = isset($_SESSION['user_id']);
        $data['view'] = $view;

        extract($data, EXTR_SKIP);

        // Require layout (single call)
        require __DIR__ . '/../Views/layouts/main.php';
    }

    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}