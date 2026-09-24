<?php
namespace App\Services;
use App\Core\SessionContext;
use App\Core\Validation;
use App\Repositories\CartRepository;
class CartService
{
    public function __construct(
        private CartRepository $repo,
        private SessionContext $session,
    ) {}
    public function getCart(): array
    {
        return array_map(function ($item) {
            $item["quantity"] = (int) $item["quantity"];
            $item["stock"] = (int) $item["stock"];
            $item["price"] = (float) $item["price"];
            $item["subtotal"] = round($item["price"] * $item["quantity"], 2);
            $item["available"] =
                $item["deleted_at"] === null &&
                $item["quantity"] > 0 &&
                $item["quantity"] <= $item["stock"];
            return $item;
        }, $this->repo->items($this->session->cartKey()));
    }
    public function addToCart(int $productId, int $qty = 1): void
    {
        $this->repo->setQuantity(
            $this->session->cartKey(),
            $productId,
            Validation::integer($qty),
            true,
        );
    }
    public function update(int $id, int $qty): void
    {
        $this->repo->update(
            $this->session->cartKey(),
            $id,
            Validation::integer($qty),
        );
    }
    public function remove(int $id): void
    {
        $this->repo->remove($this->session->cartKey(), $id);
    }
    public function count(): int
    {
        return array_sum(array_column($this->getCart(), "quantity"));
    }
}
