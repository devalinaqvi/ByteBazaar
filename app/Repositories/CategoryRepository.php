<?php

namespace App\Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository
{
    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, Category::class);
    }

    public function find(int $id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetchObject(Category::class);
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $params = [
            ':name' => $data['name'] ?? null,
            ':slug' => $data['slug'] ?? null
        ];
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, slug)
            VALUES (:name, :slug)
        ");
        $stmt->execute($params);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $params = [
            ':name' => $data['name'] ?? null,
            ':slug' => $data['slug'] ?? null,
            ':id' => $id
        ];
        $stmt = $this->db->prepare("
            UPDATE categories SET
                name = :name,
                slug = :slug
            WHERE id = :id
        ");
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
