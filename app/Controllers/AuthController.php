<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, HttpException, Validation};
use App\Services\AuthService;
class AuthController extends BaseController
{
    public function __construct(
        private AuthService $auth,
        private Request $request,
        private \App\Services\LoginLimiter $limiter,
    ) {}
    public function login(): void
    {
        $this->render("pages/login", ["title" => "Welcome back"]);
    }
    public function register(): void
    {
        $this->render("pages/register", ["title" => "Create your account"]);
    }
    public function authenticate(): void
    {
        $data = $this->request->all();
        $email = Validation::email($data);
        $password = $data["password"] ?? "";
        if (
            !is_string($password) ||
            $password === "" ||
            strlen($password) > 72
        ) {
            throw new HttpException(422, "Enter a valid password.");
        }
        $this->limiter->attempt($email, $_SERVER["REMOTE_ADDR"] ?? "local");
        $user = $this->auth->authenticate($email, $password);
        if (!$user) {
            throw new HttpException(
                422,
                "The email or password is incorrect. Please try again.",
            );
        }
        $this->auth->login($user);
        $this->done($user->is_admin ? "/admin" : "/user/dashboard");
    }
    public function postRegister(): void
    {
        $this->auth->register($this->request->all());
        $this->done("/user/dashboard");
    }
    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect("/");
    }
    private function done(string $path): void
    {
        if (wants_json()) {
            $this->json([
                "success" => true,
                "redirect" => url_path(ltrim($path, "/")),
            ]);
        } else {
            $this->redirect($path);
        }
    }
}
