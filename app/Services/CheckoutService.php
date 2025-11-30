<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use PDOException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService
    ) {}

    public function checkout(int $userId)
    {
        $items = $this->cartService->getCart();

        if (empty($items)) {
            throw new \Exception("Cart is empty");
        }

        $orderId = $this->orderService->createOrder($userId, $items);

        // Clear cart
        foreach ($items as $item) {
            $this->cartService->remove($item->id);
        }

        return $orderId;
    }
}
