<?php

namespace App\Repositories;

use App\Models\Order;
use PDO;

class OrderRepository
{
    public function __construct(private readonly PDO $db) {}

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, Order::class);
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Order::class);
    }
    public function create(?int $userId, array $data, array $items): int
    {
        logMessage('Creating order');
        try {
            $this->db->beginTransaction();

            // Calculate total price from CartItem objects
            $total = 0;
            foreach ($items as $item) {
                $total += ($item->quantity * $item->product->price);
            }
            logMessage('Order total: ' . $total);

            // Add shipping and tax
            $shipping = 5.00;
            $tax = ($total + $shipping) * 0.08;
            $total = $total + $shipping + $tax;

            // Insert into orders table
            $stmt = $this->db->prepare("
            INSERT INTO orders 
            (user_id, customer_name, customer_email, shipping_address, shipping_city, 
             shipping_country, shipping_postal_code, phone, payment_method, total_price, status) 
            VALUES (:user_id, :customer_name, :customer_email, :shipping_address, :shipping_city,
                    :shipping_country, :shipping_postal_code, :phone, :payment_method, :total_price, :status)
        ");

            $stmt->execute([
                ':user_id'             => $userId,
                ':customer_name'       => $data['customer_name'],
                ':customer_email'      => $data['customer_email'],
                ':shipping_address'    => $data['shipping_address'],
                ':shipping_city'       => $data['shipping_city'],
                ':shipping_country'    => $data['shipping_country'],
                ':shipping_postal_code'=> $data['shipping_postal_code'],
                ':phone'               => $data['phone'],
                ':payment_method'      => $data['payment_method'] ?? 'cod',
                ':total_price'         => $total,
                ':status'              => 'pending'
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // Insert order items - get price from product object
            $itemStmt = $this->db->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");

            foreach ($items as $item) {
                // Price comes from $item->product->price
                $itemStmt->execute([
                    $orderId,
                    $item->product_id,
                    $item->quantity,
                    $item->product->price  // This is the product price
                ]);

                //decrement stock
                $this->decrementStock($item->product_id, $item->quantity);
            }

            $this->db->commit();

            return $orderId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            logMessage('Order creation error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function find(int $id): ?Order
    {
        // Get the order
        $stmt = "SELECT o.* FROM orders o WHERE o.id = ?";
        $stmt = $this->db->prepare($stmt);
        $stmt->execute([$id]);
        $order = $stmt->fetchObject(Order::class);

        if ($order) {
            // Get all order items with their product details
            $itemsStmt = "
            SELECT oi.*, p.name as product_name, p.price as product_price, p.image_url as product_image 
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ";
            $itemsStmt = $this->db->prepare($itemsStmt);
            $itemsStmt->execute([$id]);
            $order->items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        if(!$order)
        {
            throw new \Exception('Order not found');
        }

        return $order;
    }
    private function decrementStock(int $productId, int $quantity): void
    {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :productId");
        $stmt->execute([':quantity' => $quantity, ':productId' => $productId]);
    }
}
