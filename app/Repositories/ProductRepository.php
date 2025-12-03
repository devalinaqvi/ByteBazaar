<?php

namespace App\Repositories;

use App\Factories\ProductFactory;
use App\Models\Product;
use PDO;

class ProductRepository
{
    public function __construct(private PDO $db) {}

    public function getAll(): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id DESC"
        );
        $stmt->execute();

        return array_map(
            fn($row) => ProductFactory::fromArray($row),
            $stmt->fetchAll()
        );
    }

    public function find(int $id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM products WHERE id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? ProductFactory::fromArray($row) : null;
    }

    public function getByCategoryIds(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return $this->getAll();
        }

        $sql = "SELECT * FROM products WHERE isDeleted = 0";

        $params = [];

        // If filters selected, filter categories
        if (!empty($categoryIds)) {
            // Create placeholders (?, ?, ?)
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $sql .= " AND category_id IN ($placeholders)";
            $params = array_merge($params, $categoryIds);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn($row) => ProductFactory::fromArray($row),
            $stmt->fetchAll()
        );
    }

    public function findBySlug(string $slug): ?Product
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM products WHERE slug = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$slug]);
        $row = $stmt->fetch();

        return $row ? ProductFactory::fromArray($row) : null;
    }

    public function create(array $data): int
    {
        $final = render_description($data['description'], $data);
        // Map input data to SQL placeholders
        $params = [
            ':name' => $data['name'] ?? null,
            ':slug' => $data['slug'] ?? null,
            ':description' => $final,
            ':price' => $data['price'] ?? null,
            ':stock' => $data['stock'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
        ];

        $stmt = $this->db->prepare("
        INSERT INTO products (name, slug, description, price, stock, category_id, image_url)
        VALUES (:name, :slug, :description, :price, :stock, :category_id, :image_url)
    ");

        $stmt->execute($params);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // Map input data to SQL placeholders (filter out extra fields)
        $params = [
            ':name' => $data['name'] ?? null,
            ':slug' => $data['slug'] ?? null,
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'] ?? null,
            ':stock' => $data['stock'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':image' => $data['image'] ?? null,
            ':id' => $id
        ];

        $stmt = $this->db->prepare("
            UPDATE products SET
                name = :name,
                slug = :slug,
                description = :description,
                price = :price,
                stock = :stock,
                category_id = :category_id,
                image_url = :image,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products SET deleted_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
    public function searchPaginated(string $search = '', int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM products 
            WHERE deleted_at IS NULL 
              AND (name LIKE :search OR slug LIKE :search)
            ORDER BY id DESC
            LIMIT :offset, :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":search", "%{$search}%", PDO::PARAM_STR);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return array_map(fn($r) => ProductFactory::fromArray($r), $rows);
    }

    public function countSearch(string $search = ''): int
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) FROM products
        WHERE deleted_at IS NULL
          AND (name LIKE ? OR slug LIKE ?)
    ");
        $stmt->execute(["%$search%", "%$search%"]);
        return (int)$stmt->fetchColumn();
    }

}
