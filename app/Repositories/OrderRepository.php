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
    public function create(int $userId, array $items): int
    {
        $stmt = $this->db->prepare("INSERT INTO orders (user_id) VALUES (?)");
        return $stmt->execute([$userId]) ? $this->db->lastInsertId() : 0;
    }
}
