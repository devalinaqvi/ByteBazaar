<?php
namespace App\Services;
use App\Repositories\UserRepository;
readonly class UserService
{
    public function __construct(private UserRepository $userRepository) {}
    public function listing(int $page): array
    {
        return $this->userRepository->listing($page);
    }
}
