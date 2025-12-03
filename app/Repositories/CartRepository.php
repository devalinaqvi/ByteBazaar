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
        $sql .= "session_id = ?";
        $params = [$sessionId];

        logMessage('Session ID: ' . $sessionId . ' User ID: ' . $userId . ' SQL: ' . $sql);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => CartItemFactory::fromArray($row),
            $rows
        );
    }
    public function getOrderItems(?int $userId, string $sessionId): array
    {
        $sql = "
            SELECT 
                c.*, 
                p.name AS product_name,
                p.price AS product_price,
                p.image_url AS product_image,
                p.description AS product_description
            FROM cart_items c
            INNER JOIN products p ON p.id = c.product_id
            WHERE 
            c.session_id = ?";
            $params = [$sessionId];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $item = CartItemFactory::fromArray($row);

            $item->product = (object)[
                'name'        => $row['product_name'],
                'price'       => $row['product_price'],
                'image'       => $row['product_image'],
                'description' => $row['product_description'],
            ];

            return $item;
        }, $rows);
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
        INSERT INTO cart_items (session_id, product_id, quantity)
        VALUES (:session_id, :product_id, :quantity)
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
        logMessage('Deleting cart item: ' . $id);
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

    public function countItems(string $sessionId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM cart_items WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    public function countItemsByProduct(int $productId, string $session_id): int
    {
        $stmt = $this->db->prepare("
            SELECT SUM(quantity) as total_quantity FROM cart_items WHERE product_id = ? AND session_id = ?
        ");
        $stmt->execute([$productId, $session_id]);
        $result = $stmt->fetch();
        return $result['total_quantity'] ?? 0;
    }
}
