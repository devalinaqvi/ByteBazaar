<?php

namespace App\Repositories;

use App\Models\CartItem;
use PDO;
class CartRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) {
     $this->pdo = $pdo;
    }

    public function getCartItems(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM cart_items WHERE user_id = :userId');
        $stmt->execute(['userId' => $userId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return new CartItem($item);
        }, $cartItems);
    }

    public function addToCart(int $userId, int $productId, int $quantity): void {
        $stmt = $this->pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:userId, :productId, :quantity)');
        $stmt->execute(['userId' => $userId, 'productId' => $productId, 'quantity' => $quantity]);
    }
    public function removeFromCart(int $userId, int $productId): void {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE user_id = :userId AND product_id = :productId');
        $stmt->execute(['userId' => $userId, 'productId' => $productId]);
    }
    public function clearCart(int $userId): void {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE user_id = :userId');
        $stmt->execute(['userId' => $userId]);
    }
    public function getCartTotal(int $userId): int {
        $stmt = $this->pdo->prepare('SELECT SUM(quantity * price) FROM cart_items WHERE user_id = :userId');
        $stmt->execute(['userId' => $userId]);
        return (int) $stmt->fetchColumn();
    }
    public function getCartCount(int $userId): int {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM cart_items WHERE user_id = :userId');
        $stmt->execute(['userId' => $userId]);
        return (int) $stmt->fetchColumn();
    }
    public function getItem(int $userId, int $productId): ?CartItem {
        $stmt = $this->pdo->prepare('SELECT * FROM cart_items WHERE user_id = :userId AND product_id = :productId');
        $stmt->execute(['userId' => $userId, 'productId' => $productId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cartItem ? new CartItem($cartItem) : null;
    }
}