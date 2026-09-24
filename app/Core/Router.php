<?php
namespace App\Core;
use AltoRouter;
class Router
{
    private AltoRouter $router;
    public function __construct(private Container $container)
    {
        $this->router = new AltoRouter();
    }
    public function get(string $path, string $target, string $name = ""): self
    {
        $this->router->map("GET", $path, $target, $name);
        return $this;
    }
    public function post(string $path, string $target, string $name = ""): self
    {
        $this->router->map("POST", $path, $target, $name);
        return $this;
    }
    public function dispatch(string $uri, string $method): void
    {
        try {
            $path = parse_url($uri, PHP_URL_PATH) ?: "/";
            $base = url_path();
            if (
                $base &&
                ($path === $base || str_starts_with($path, $base . "/"))
            ) {
                $path = substr($path, strlen($base));
            }
            $path = rtrim($path, "/") ?: "/";
            $match = $this->router->match($path, $method);
            if (!$match) {
                throw new HttpException(404, "This page could not be found.");
            }
            $session = $this->container->get("session");
            if (
                $method === "POST" &&
                !$session->validCsrf(
                    $_POST["csrf_token"] ??
                        ($_SERVER["HTTP_X_CSRF_TOKEN"] ?? null),
                )
            ) {
                throw new HttpException(
                    403,
                    "Your session has changed. Refresh the page and try again.",
                );
            }
            $name = $match["name"] ?? "";
            $user = $this->container->get("authService")->getCurrentUser();
            if (
                str_starts_with($name, "admin_") ||
                str_starts_with($name, "protected_")
            ) {
                if (!$user) {
                    if (wants_json()) {
                        throw new HttpException(
                            401,
                            "Please sign in to continue.",
                        );
                    }
                    header("Location: " . url_path("login"), true, 302);
                    return;
                }
                if (str_starts_with($name, "admin_") && !$user["is_admin"]) {
                    throw new HttpException(
                        403,
                        "You do not have access to this page.",
                    );
                }
            }
            if (str_starts_with($name, "guest_") && $user) {
                header("Location: " . url_path("user/dashboard"), true, 302);
                return;
            }
            [$controller, $action] = explode("@", $match["target"]);
            $instance = $this->container->get(
                "\\App\\Controllers\\" . $controller,
            );
            if ($instance instanceof BaseController) {
                $instance->setRenderer($this->container->get("view"));
            }
            $instance->$action(...array_values($match["params"]));
        } catch (\Throwable $e) {
            $status = $e instanceof HttpException ? $e->status : 500;
            if ($status === 500) {
                error_log(get_class($e) . ": " . $e->getMessage());
            }
            $message =
                $status === 500
                    ? "Something went wrong. Please try again."
                    : $e->getMessage();
            http_response_code($status);
            if (wants_json()) {
                header("Content-Type: application/json");
                echo json_encode(["success" => false, "message" => $message]);
            } else {
                $title = "Unable to complete request";
                $view = "pages/error";
                $error = $message;
                require base_path("app/Views/layouts/main.php");
            }
        }
    }
}
