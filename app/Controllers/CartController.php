<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, Validation};
use App\Services\CartService;
class CartController extends BaseController
{
    public function __construct(
        private CartService $cart,
        private Request $request,
    ) {}
    public function index(): void
    {
        $items = $this->cart->getCart();
        $this->render("pages/cart", [
            "title" => "Your bag",
            "cartItems" => $items,
            "cartTotal" => array_sum(array_column($items, "subtotal")),
        ]);
    }
    public function api_index(): void
    {
        $this->json([
            "items" => $this->cart->getCart(),
            "cart_count" => $this->cart->count(),
        ]);
    }
    public function add(int $productId): void
    {
        $this->cart->addToCart(
            $productId,
            Validation::integer($this->request->post("qty", 1)),
        );
        $this->respond("Added to your bag.");
    }
    public function remove(int $itemId): void
    {
        $this->cart->remove($itemId);
        $this->respond("Item removed.");
    }
    public function update(): void
    {
        $this->cart->update(
            Validation::integer($this->request->post("id"), 1, PHP_INT_MAX),
            Validation::integer($this->request->post("qty")),
        );
        $this->respond("Bag updated.");
    }
    private function respond(string $message): void
    {
        if (wants_json()) {
            $this->json([
                "success" => true,
                "message" => $message,
                "cart_count" => $this->cart->count(),
            ]);
        } else {
            $this->redirect("/cart");
        }
    }
}
