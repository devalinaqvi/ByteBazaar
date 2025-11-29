<?php

namespace App\Services;

use App\Repositories\CartRepository;
use Exception;
class CartService
{
    private CartRepository $cartRepository;
    public function __construct(CartRepository $cartRepository) {
        $this->cartRepository = $cartRepository;
    }
    public function getCartItems(int $userId): array {
        return $this->cartRepository->getCartItems($userId);
    }
    public function addToCart(int $userId, int $productId, int $quantity): void {
        $this->cartRepository->addToCart($userId, $productId, $quantity);
    }
    public function removeFromCart(int $userId, int $productId): void {
        $this->cartRepository->removeFromCart($userId, $productId);
    }
    public function clearCart(int $userId): void {
        $this->cartRepository->clearCart($userId);
    }
    public function getCartTotal(int $userId): int {
        return $this->cartRepository->getCartTotal($userId);
    }
    public function getCartCount(int $userId): int {
        return $this->cartRepository->getCartCount($userId);
    }
    public function getItem(int $userId, int $productId): \App\Models\CartItem
    {
        return $this->cartRepository->getItem($userId, $productId);
    }
}