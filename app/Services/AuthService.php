<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    public function __construct(private readonly UserRepository $userRepository) {}
    public function getCurrentUser(): ?array {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'is_admin' => $_SESSION['is_admin'] ?? false,
            'logged_in' => $_SESSION['logged_in'] ?? false
        ];
    }

    public function login(\App\Models\User $user): void {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['is_admin'] = $user->is_admin;
        $_SESSION['logged_in'] = true;
        session_regenerate_id(true);  // Security: prevent fixation
    }

    public function register(array $data): void {
        logMessage("AUTH SERVICE: Registering user with email: {$data['email']}");
        $userId = $this->userRepository->create($data['name'], $data['email'], $data['password']);
        $user = $this->userRepository->findById($userId);
        logMessage("AUTH SERVICE: Result: success (user created)");
        $this->login($user);
    }

    public function logout(): void {
        session_destroy();
    }

    public function authenticate(string $email, string $password): ?\App\Models\User
    {
        if ($email === '' && $password === '') {
            return null;
        }
        $user = $this->userRepository->findByEmail($email);
        logMessage("AUTH SERVICE: Authenticating user with email: {$email}");
        if (!$user) {
            logMessage("AUTH SERVICE: Result: failure (user not found)");
            return null;
        }

        // Debug logging
        logMessage("AUTH SERVICE: Plain password length: " . strlen($password));
        logMessage("AUTH SERVICE: Hashed password from DB: " . $user->password);
        logMessage("AUTH SERVICE: Hashed password length: " . strlen($user->password));

        $isValid = password_verify($password, $user->password);
        logMessage("AUTH SERVICE: password_verify result: " . ($isValid ? 'TRUE' : 'FALSE'));

        if (!$isValid) {
            logMessage("AUTH SERVICE: Result: failure (password mismatch)");
            return null;
        }
        logMessage("AUTH SERVICE: Result: success");
        return $user;
    }

    public function isUserAdmin(): bool|array {
        $user = $this->getCurrentUser();
        return $user ? $user['is_admin'] : false;
    }

    public function emailExists(string $email): bool {
        return $this->userRepository->findByEmail($email) !== null;
    }
}