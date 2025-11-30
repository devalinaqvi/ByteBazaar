<?php

namespace App\Services;

use App\Repositories\CartRepository;

class CartService
{
    public function __construct(private CartRepository $cartRepo) {}

    private function identifyUser(): array
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!isset($_SESSION['session_id'])) {
            $_SESSION['session_id'] = bin2hex(random_bytes(16));
        }

        return [$userId, $_SESSION['session_id']];
    }

    public function getCart(): array
    {
        [$userId, $sessionId] = $this->identifyUser();

        return $this->cartRepo->getCartItems($userId, $sessionId);
    }

    public function addToCart(int $productId, int $qty = 1)
    {
        logMessage('Adding product to cart');
        [$userId, $sessionId] = $this->identifyUser();

        logMessage('Identified user: ' . $userId);
        logMessage('Identified session: ' . $sessionId);

        $existing = $this->cartRepo->findItem($userId, $sessionId, $productId);

        logMessage('Adding product to cart: User ID: ' . $userId . ', Session ID: ' . $sessionId . ', Product ID: ' . $productId . ', Quantity: ' . $qty);

        if ($existing) {
            logMessage('Updating existing cart item');
            return $this->cartRepo->updateQuantity($existing->id, $existing->quantity + $qty);
        }

        logMessage('Adding new cart item');
        return $this->cartRepo->addItem([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => $qty,
        ]);
    }

    public function remove(int $id)
    {
        return $this->cartRepo->deleteItem($id);
    }

    public function update(int $id, int $qty)
    {
        return $this->cartRepo->updateQuantity($id, $qty);
    }

    public function transferSessionCartToUser(int $userId)
    {
        $sessionId = $_SESSION['session_id'] ?? null;

        if ($sessionId) {
            $this->cartRepo->transferSessionToUser($sessionId, $userId);
        }
    }
}
