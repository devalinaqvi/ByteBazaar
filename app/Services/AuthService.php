<?php

namespace App\Services;

class AuthService
{

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
    public function logout(): void {
        session_destroy();
    }
    public function getCurrentUser() {
        return $_SESSION['user_id'] ?? null;
    }
}