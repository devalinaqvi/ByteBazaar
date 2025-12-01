<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService
{
    public function __construct(private readonly OrderRepository $orders) {}

    public function listAllOrders(): array
    {
        return $this->orders->getAll();
    }

    public function listUserOrders(int $userId): array
    {
        return $this->orders->getByUser($userId);
    }

    public function createOrder(?int $userId, array $data, array $items): int
    {
        return $this->orders->create($userId, $data, $items);
    }
    public function getOrderDetails(int $orderId): \App\Models\Order
    {
        $order = $this->orders->find($orderId);
        logMessage('Order details: ' . print_r($order, true));
        if (!$order) {
            throw new \Exception('Order not found');
        }
        return $order;
    }
}
?>
