<?php
namespace App\Repositories;
use PDO;
use App\Core\HttpException;
use App\Services\Pricing;
class OrderRepository
{
    public function __construct(private PDO $db) {}
    public function listing(
        ?int $userId = null,
        int $page = 1,
        int $limit = 20,
    ): array {
        $where = $userId === null ? "" : " WHERE user_id = ?";
        $params = $userId === null ? [] : [$userId];
        $s = $this->db->prepare("SELECT COUNT(*) FROM orders" . $where);
        $s->execute($params);
        $total = (int) $s->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / $limit))));
        $s = $this->db->prepare(
            "SELECT * FROM orders" .
                $where .
                " ORDER BY id DESC LIMIT " .
                $limit .
                " OFFSET " .
                ($page - 1) * $limit,
        );
        $s->execute($params);
        return [
            "orders" => $s->fetchAll(),
            "total" => $total,
            "page" => $page,
            "pages" => max(1, (int) ceil($total / $limit)),
        ];
    }
    public function getAll(): array
    {
        return $this->listing()["orders"];
    }
    public function getByUser(int $id): array
    {
        return $this->listing($id)["orders"];
    }
    public function find(int $id): ?array
    {
        $s = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $s->execute([$id]);
        $order = $s->fetch();
        if (!$order) {
            return null;
        }
        $s = $this->db->prepare(
            "SELECT oi.*, COALESCE(oi.product_name, p.name) AS product_name, p.image_url AS product_image FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE order_id = ?",
        );
        $s->execute([$id]);
        $order["items"] = $s->fetchAll();
        return $order;
    }
    public function place(
        string $owner,
        ?int $userId,
        array $data,
        string $key,
    ): int {
        $this->db->beginTransaction();
        try {
            $lock =
                $this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === "mysql"
                    ? " FOR UPDATE"
                    : "";
            if ($userId) {
                $s = $this->db->prepare(
                    "SELECT id FROM users WHERE id = ?" . $lock,
                );
                $s->execute([$userId]);
            }
            $s = $this->db->prepare(
                "SELECT id FROM orders WHERE checkout_key = ? AND cart_owner = ?",
            );
            $s->execute([$key, $owner]);
            if ($id = $s->fetchColumn()) {
                $this->db->commit();
                return (int) $id;
            }
            $s = $this->db->prepare(
                "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.stock, p.deleted_at FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.session_id = ? ORDER BY p.id" .
                    $lock,
            );
            $s->execute([$owner]);
            $items = $s->fetchAll();
            if (!$items) {
                throw new HttpException(
                    422,
                    "Your bag is empty. Add a product before checking out.",
                );
            }
            foreach ($items as $item) {
                if (
                    $item["quantity"] < 1 ||
                    $item["quantity"] > 999 ||
                    $item["deleted_at"] !== null
                ) {
                    throw new HttpException(
                        422,
                        "Your bag contains an unavailable item. Please review it.",
                    );
                }
                $s = $this->db->prepare(
                    "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ? AND deleted_at IS NULL",
                );
                $s->execute([
                    $item["quantity"],
                    $item["product_id"],
                    $item["quantity"],
                ]);
                if ($s->rowCount() !== 1) {
                    throw new HttpException(
                        409,
                        $item["name"] .
                            " no longer has enough stock. Please update your bag.",
                    );
                }
            }
            $totals = Pricing::totals($items);
            $values = array_merge($data, [
                "user_id" => $userId,
                "cart_owner" => $owner,
                "checkout_key" => $key,
                "subtotal" => $totals["subtotal"],
                "shipping_amount" => $totals["shipping"],
                "tax_amount" => $totals["tax"],
                "total_price" => $totals["total"],
                "status" => "pending",
            ]);
            $columns = array_keys($values);
            $s = $this->db->prepare(
                "INSERT INTO orders (" .
                    implode(",", $columns) .
                    ") VALUES (" .
                    implode(",", array_fill(0, count($columns), "?")) .
                    ")",
            );
            $s->execute(array_values($values));
            $id = (int) $this->db->lastInsertId();
            $s = $this->db->prepare(
                "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)",
            );
            foreach ($items as $item) {
                $s->execute([
                    $id,
                    $item["product_id"],
                    $item["name"],
                    $item["quantity"],
                    $item["price"],
                ]);
            }
            $s = $this->db->prepare(
                "DELETE FROM cart_items WHERE session_id = ?",
            );
            $s->execute([$owner]);
            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    public function updateStatus(int $id, string $status): void
    {
        // Cancellation/refunds need an explicit stock/payment workflow, not a free-form status.
        $previous = [
            "processing" => "pending",
            "shipped" => "processing",
            "delivered" => "shipped",
        ];
        if (!isset($previous[$status])) {
            throw new HttpException(422, "Choose a valid next order status.");
        }
        $s = $this->db->prepare(
            "UPDATE orders SET status = ? WHERE id = ? AND status = ?",
        );
        $s->execute([$status, $id, $previous[$status]]);
        if (!$s->rowCount()) {
            throw new HttpException(
                409,
                "The order changed or this status transition is not allowed.",
            );
        }
    }
}
