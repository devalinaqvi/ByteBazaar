<?php
namespace App\Core;
class BaseController
{
    private ViewRenderer $renderer;
    public function setRenderer(ViewRenderer $renderer): void
    {
        $this->renderer = $renderer;
    }
    public function __construct() {}
    protected function redirect(string $url, array $params = []): void
    {
        header(
            "Location: " .
                url_path(ltrim($url, "/") ?: "/") .
                ($params ? "?" . http_build_query($params) : ""),
            true,
            303,
        );
    }
    protected function render(string $view, array $data = []): void
    {
        $this->renderer->render($view, $data);
    }
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}
