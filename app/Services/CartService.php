<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use Random\RandomException;

class CartService
{
    public function __construct(private CartRepository $cartRepo, private ProductRepository $productRepo) {}

    /**
     * @throws RandomException
     */
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

    public function getCartWithProducts(?int $userId, string $sessionId): array
    {
        [$userId, $sessionId] = $this->identifyUser();

        return $this->cartRepo->getOrderItems($userId, $sessionId);
    }

    public function clearCart(?int $userId, string $sessionId): void
    {
            // Clear session cart
            [$userId, $sessionId] = $this->identifyUser();
            $_SESSION['cart_count'] = 0;
            $this->cartRepo->clearSessionCart($sessionId);
    }


    public function getItemPrice(int $productId): float
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            return 0;
        }
        return $product->price;
    }

    public function getProductImage(int $productId): string
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            return '';
        }
        return $product->image_url;
    }
    public function getProductSlug(int $productId): string
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            return '';
        }
        return $product->slug;
    }
    public function getProductName(int $productId): string
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            return '';
        }
        return $product->name;
    }

    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function addToCart(int $productId, int $qty = 1)
    {
        logMessage('Adding product to cart');

        [$userId, $sessionId] = $this->identifyUser();

        logMessage('Identified session: ' . $sessionId);

        $existing = $this->cartRepo->findItem($userId, $sessionId, $productId);

        $this->checkAvailability($productId, $sessionId);

        logMessage('Adding product to cart: User ID: ' . $userId . ', Session ID: ' . $sessionId . ', Product ID: ' . $productId . ', Quantity: ' . $qty);

        if ($existing) {
            logMessage('Updating existing cart item');
            return $this->cartRepo->updateQuantity($existing->id, $existing->quantity + $qty);
        }

        logMessage('Adding new cart item');
        return $this->cartRepo->addItem([
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => $qty,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function checkAvailability(int $productId, string $sessionId): void
    {
        $product = $this->productRepo->find($productId);
        if (!$product) {
            throw new \Exception('Product not found');
        }
        if ($product->stock < $this->cartRepo->countItemsByProduct($productId, $sessionId)) {
            throw new \Exception('Out of stock');
        }
    }

    public function remove(int $id)
    {
        return $this->cartRepo->deleteItem($id);
    }

    public function update(int $id, int $qty)
    {
        return $this->cartRepo->updateQuantity($id, $qty);
    }

    public function count(): int
    {
        [$userId, $sessionId] = $this->identifyUser();
        return $this->cartRepo->countItems($sessionId) ?? 0;
    }

    public function transferSessionCartToUser(int $userId)
    {
        $sessionId = $_SESSION['session_id'] ?? null;

        if ($sessionId) {
            $this->cartRepo->transferSessionToUser($sessionId, $userId);
        }
    }
}
