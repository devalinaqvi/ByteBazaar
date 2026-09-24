<?php
namespace App\Services;
use App\Core\{SessionContext, Validation, HttpException};
use App\Repositories\{UserRepository, CartRepository};
use App\Models\User;
class AuthService
{
    public function __construct(
        private UserRepository $users,
        private SessionContext $session,
        private CartRepository $carts,
    ) {}
    public function getCurrentUser(): ?array
    {
        $id = $this->session->userId();
        if (!$id) {
            return null;
        }
        $user = $this->users->findById($id);
        if (!$user) {
            $this->session->logout();
            return null;
        }
        return [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "is_admin" => (bool) $user->is_admin,
        ];
    }
    public function login(User $user): void
    {
        $this->carts->merge($this->session->cartKey(), "user:" . $user->id);
        $this->session->authenticate($user->id);
    }
    public function register(array $data): void
    {
        $name = Validation::text($data, "name", 100);
        $email = Validation::email($data);
        $password = $data["password"] ?? "";
        if (
            !is_string($password) ||
            strlen($password) < 12 ||
            strlen($password) > 72
        ) {
            throw new HttpException(
                422,
                "Use a password between 12 and 72 characters.",
            );
        }
        if ($password !== ($data["c-password"] ?? null)) {
            throw new HttpException(422, "Passwords do not match.");
        }
        if ($this->users->findByEmail($email)) {
            throw new HttpException(
                422,
                "An account with this email already exists.",
            );
        }
        try {
            $id = $this->users->create($name, $email, $password);
        } catch (\PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new HttpException(
                    422,
                    "An account with this email already exists.",
                );
            }
            throw $e;
        }
        $this->login($this->users->findById($id));
    }
    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));
        // A fixed dummy hash keeps nonexistent-account checks comparable in cost.
        $valid = password_verify(
            $password,
            $user?->password ??
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',
        );
        return $valid ? $user : null;
    }
    public function logout(): void
    {
        $this->session->logout();
    }
}
