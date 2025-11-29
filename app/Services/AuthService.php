<?php
namespace App\Services;

class AuthService {
    public function getCurrentUser(): ?array {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }

    public function login(int $userId, string $name, bool $isAdmin = false): void {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['is_admin'] = $isAdmin;
        session_regenerate_id(true);  // Security: prevent fixation
    }

    public function logout(): void {
        session_destroy();
    }

    // Stub for full module: register, authenticate (password_verify)
    public function authenticate(string $email, string $password): ?array {
        // Implement with UserRepo later
        return null;
    }

    public function isUserAdmin(): bool|array {
        $user = $this->getCurrentUser();
        return $user ? $user['is_admin'] : false;
    }
}