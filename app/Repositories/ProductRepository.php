<?php
namespace App\Repositories;
use PDO;
use App\Models\Product;
class ProductRepository
{
    public function __construct(private PDO $db) {}
    public function catalog(
        string $q = "",
        array $categories = [],
        int $page = 1,
        string $sort = "newest",
        int $limit = 12,
    ): array {
        $where = "deleted_at IS NULL";
        $params = [];
        if ($q !== "") {
            $where .= " AND (name LIKE ? OR description LIKE ?)";
            $params = ["%" . $q . "%", "%" . $q . "%"];
        }
        if ($categories) {
            $where .=
                " AND category_id IN (" .
                implode(",", array_fill(0, count($categories), "?")) .
                ")";
            $params = array_merge($params, $categories);
        }
        $s = $this->db->prepare(
            "SELECT COUNT(*) FROM products WHERE " . $where,
        );
        $s->execute($params);
        $total = (int) $s->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / $limit))));
        $order =
            [
                "newest" => "id DESC",
                "price_asc" => "price ASC, id DESC",
                "price_desc" => "price DESC, id DESC",
                "name" => "name ASC, id DESC",
            ][$sort] ?? "id DESC";
        $s = $this->db->prepare(
            "SELECT * FROM products WHERE " .
                $where .
                " ORDER BY " .
                $order .
                " LIMIT " .
                $limit .
                " OFFSET " .
                ($page - 1) * $limit,
        );
        $s->execute($params);
        return [
            "products" => array_map(fn($r) => new Product($r), $s->fetchAll()),
            "total" => $total,
            "page" => $page,
            "pages" => max(1, (int) ceil($total / $limit)),
        ];
    }
    public function getAll(): array
    {
        return $this->catalog()["products"];
    }
    public function find(int $id): ?Product
    {
        $s = $this->db->prepare(
            "SELECT * FROM products WHERE id = ? AND deleted_at IS NULL",
        );
        $s->execute([$id]);
        $row = $s->fetch();
        return $row ? new Product($row) : null;
    }
    public function findBySlug(string $slug): ?Product
    {
        $s = $this->db->prepare(
            "SELECT * FROM products WHERE slug = ? AND deleted_at IS NULL",
        );
        $s->execute([$slug]);
        $row = $s->fetch();
        return $row ? new Product($row) : null;
    }
    public function slugExists(string $slug, int $except = 0): bool
    {
        $s = $this->db->prepare(
            "SELECT id FROM products WHERE slug = ? AND id <> ?",
        );
        $s->execute([$slug, $except]);
        return (bool) $s->fetchColumn();
    }
    public function create(array $data): int
    {
        $s = $this->db->prepare(
            "INSERT INTO products (name, slug, description, price, stock, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)",
        );
        $s->execute([
            $data["name"],
            $data["slug"],
            $data["description"],
            $data["price"],
            $data["stock"],
            $data["category_id"],
            $data["image_url"] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool
    {
        $s = $this->db->prepare(
            "UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock = ?, category_id = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND deleted_at IS NULL",
        );
        return $s->execute([
            $data["name"],
            $data["slug"],
            $data["description"],
            $data["price"],
            $data["stock"],
            $data["category_id"],
            $data["image_url"] ?? null,
            $id,
        ]);
    }
    public function softDelete(int $id): bool
    {
        $s = $this->db->prepare(
            "UPDATE products SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?",
        );
        return $s->execute([$id]);
    }
}
