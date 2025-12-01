<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function listAllUsers(): array {
        return $this->userRepository->listAllUsers();
    }
}