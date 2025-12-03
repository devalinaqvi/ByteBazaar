<?php

namespace App\Services;

use App\Repositories\UserRepository;

readonly class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function listAllUsers(): array {
        return $this->userRepository->listAllUsers();
    }

    public function listAllOrders(int $userId): array {
        return $this->userRepository->listAllOrders($userId);
    }
}