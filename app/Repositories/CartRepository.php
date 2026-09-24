<?php
namespace App\Repositories;
use PDO;
use App\Core\HttpException;
class CartRepository
{
    public function __construct(private PDO $db) {}
    public function items(string $owner): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.product_id, c.quantity, p.name AS product_name, p.slug AS product_slug, p.price, p.image_url AS image, p.stock, p.deleted_at FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.session_id = ? ORDER BY c.id",
        );
        $stmt->execute([$owner]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function setQuantity(
        string $owner,
        int $productId,
        int $quantity,
        bool $add = false,
        ?int $expectedId = null,
    ): void {
        $this->db->beginTransaction();
        try {
            // Account first, then product: the same lock order used by checkout.
            $lock =
                $this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === "mysql"
                    ? " FOR UPDATE"
                    : "";
            if (str_starts_with($owner, "user:")) {
                $u = $this->db->prepare(
                    "SELECT id FROM users WHERE id = ?" . $lock,
                );
                $u->execute([(int) substr($owner, 5)]);
            }
            $s = $this->db->prepare(
                "SELECT stock FROM products WHERE id = ? AND deleted_at IS NULL" .
                    $lock,
            );
            $s->execute([$productId]);
            $stock = $s->fetchColumn();
            if ($stock === false) {
                throw new HttpException(
                    404,
                    "This product is no longer available.",
                );
            }
            $s = $this->db->prepare(
                "SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?",
            );
            $s->execute([$owner, $productId]);
            $item = $s->fetch();
            if (
                $expectedId !== null &&
                (!$item || (int) $item["id"] !== $expectedId)
            ) {
                throw new HttpException(
                    409,
                    "Your bag changed. Refresh it and try again.",
                );
            }
            $quantity += $add && $item ? (int) $item["quantity"] : 0;
            if ($quantity < 1 || $quantity > 999 || $quantity > (int) $stock) {
                throw new HttpException(
                    422,
                    "The requested quantity is not available.",
                );
            }
            if ($item) {
                $s = $this->db->prepare(
                    "UPDATE cart_items SET quantity = ? WHERE id = ? AND session_id = ?",
                );
                $s->execute([$quantity, $item["id"], $owner]);
            } else {
                $s = $this->db->prepare(
                    "INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)",
                );
                $s->execute([$owner, $productId, $quantity]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    public function update(string $owner, int $id, int $quantity): void
    {
        $s = $this->db->prepare(
            "SELECT product_id FROM cart_items WHERE id = ? AND session_id = ?",
        );
        $s->execute([$id, $owner]);
        $product = $s->fetchColumn();
        if ($product === false) {
            throw new HttpException(404, "Cart item not found.");
        }
        $this->setQuantity($owner, (int) $product, $quantity, false, $id);
    }
    public function remove(string $owner, int $id): void
    {
        $s = $this->db->prepare(
            "DELETE FROM cart_items WHERE id = ? AND session_id = ?",
        );
        $s->execute([$id, $owner]);
        if (!$s->rowCount()) {
            throw new HttpException(404, "Cart item not found.");
        }
    }
    public function clear(string $owner): void
    {
        $s = $this->db->prepare("DELETE FROM cart_items WHERE session_id = ?");
        $s->execute([$owner]);
    }
    public function merge(string $guest, string $account): void
    {
        if ($guest === $account) {
            return;
        }
        $this->db->beginTransaction();
        try {
            // Login to the same account in two browsers cannot overwrite a merge.
            $lock =
                $this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === "mysql"
                    ? " FOR UPDATE"
                    : "";
            $s = $this->db->prepare(
                "SELECT id FROM users WHERE id = ?" . $lock,
            );
            $s->execute([(int) substr($account, 5)]);
            foreach ($this->items($guest) as $item) {
                if ($item["deleted_at"] !== null || (int) $item["stock"] < 1) {
                    continue;
                }
                $s = $this->db->prepare(
                    "SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?",
                );
                $s->execute([$account, $item["product_id"]]);
                $existing = $s->fetch();
                $qty = min(
                    999,
                    (int) $item["stock"],
                    max(0, (int) $item["quantity"]) +
                        ($existing ? max(0, (int) $existing["quantity"]) : 0),
                );
                if ($qty < 1) {
                    continue;
                }
                if ($existing) {
                    $s = $this->db->prepare(
                        "UPDATE cart_items SET quantity = ? WHERE id = ?",
                    );
                    $s->execute([$qty, $existing["id"]]);
                } else {
                    $s = $this->db->prepare(
                        "INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)",
                    );
                    $s->execute([$account, $item["product_id"], $qty]);
                }
            }
            $this->clear($guest);
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
