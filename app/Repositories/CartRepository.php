<?php

namespace App\Repositories;

use App\Factories\CartItemFactory;
use PDO;

class CartRepository
{
    public function __construct(private PDO $db) {}

    public function getCartItems(?int $userId, string $sessionId): array
    {
        $sql = "SELECT * FROM cart_items WHERE ";

        if ($userId) {
            $sql .= "user_id = ?";
            $params = [$userId];
        } else {
            $sql .= "session_id = ?";
            $params = [$sessionId];
        }

        logMessage('Session ID: ' . $sessionId . ' User ID: ' . $userId . ' SQL: ' . $sql);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => CartItemFactory::fromArray($row),
            $rows
        );
    }

    public function findItem(?int $userId, string $sessionId, int $productId)
    {
        logMessage('Finding cart item: User ID: ' . $userId . ', Session ID: ' . $sessionId . ', Product ID: ' . $productId);
        $sql = "SELECT * FROM cart_items WHERE product_id = ? AND ";

        if ($sessionId) {
            $sql .= "session_id = ?";
            $params = [$productId, $sessionId];
        }
        logMessage('SQL: ' . $sql);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $data = $stmt->fetch();
        logMessage('Found cart item: ' . json_encode($data));
        return $data ? CartItemFactory::fromArray($data) : null;
    }

    public function addItem(array $data): int
    {
        // Debug: Check if product exists
        $checkStmt = $this->db->prepare("SELECT id FROM products WHERE id = :product_id AND deleted_at IS NULL");
        $checkStmt->execute([':product_id' => $data['product_id']]);

        if (!$checkStmt->fetch()) {
            throw new \Exception("Product ID {$data['product_id']} does not exist or is deleted");
        }

        $stmt = $this->db->prepare("
        INSERT INTO cart_items (user_id, session_id, product_id, quantity)
        VALUES (:user_id, :session_id, :product_id, :quantity)
        ");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare("
            UPDATE cart_items SET quantity = ? WHERE id = ?
        ");
        return $stmt->execute([$quantity, $id]);
    }

    public function deleteItem(int $id): bool
    {
        return $this->db->prepare("DELETE FROM cart_items WHERE id = ?")->execute([$id]);
    }

    public function clearSessionCart(string $sessionId): bool
    {
        return $this->db->prepare("DELETE FROM cart_items WHERE session_id = ?")
            ->execute([$sessionId]);
    }

    public function transferSessionToUser(string $sessionId, int $userId): void
    {
        $stmt = $this->db->prepare("
            UPDATE cart_items SET user_id = ?, session_id = NULL
            WHERE session_id = ?
        ");

        $stmt->execute([$userId, $sessionId]);
    }
}
