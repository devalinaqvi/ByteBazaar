<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService
{
    public function __construct(private OrderRepository $orders) {}

    public function listAllOrders(): array
    {
        return $this->orders->getAll();
    }

    public function listUserOrders(int $userId): array
    {
        return $this->orders->getByUser($userId);
    }
}
