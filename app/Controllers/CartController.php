<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CartService;
use Exception;

class CartController extends BaseController
{
    private CartService $cartService;
    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }
    public function add(int $userId, int $productId, int $quantity): void
    {
        session_start();
        $this->cartService->addToCart($userId, $productId, $quantity);
    }
    public function remove(int $userId, int $productId): void
    {
        $this->cartService->removeFromCart($userId, $productId);
    }
    public function clear(int $userId): void
    {
        $this->cartService->clearCart($userId);
    }
    public function getCartTotal(int $userId): int
    {
        return $this->cartService->getCartTotal($userId);
    }
}