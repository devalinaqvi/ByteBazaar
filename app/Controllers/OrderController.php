<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, SessionContext, Validation};
use App\Services\{
    OrderService,
    CheckoutService,
    CartService,
    Pricing,
    AuthService,
};
class OrderController extends BaseController
{
    public function __construct(
        private OrderService $orders,
        private CheckoutService $checkoutService,
        private CartService $cart,
        private Request $request,
        private SessionContext $session,
        private AuthService $auth,
    ) {}
    public function checkout(): void
    {
        $items = $this->cart->getCart();
        $this->render("pages/checkout", [
            "title" => "Checkout",
            "cartItems" => $items,
            "totals" => Pricing::totals($items),
            "checkout_key" => $this->checkoutService->token(),
            "customer" => $this->auth->getCurrentUser(),
        ]);
    }
    public function placeOrder(): void
    {
        $id = $this->checkoutService->place($this->request->all());
        $path = url_path("order/" . $id);
        if (wants_json()) {
            $this->json([
                "success" => true,
                "message" => "Your order is confirmed.",
                "redirect" => $path,
                "order_id" => $id,
            ]);
        } else {
            $this->redirect("/order/" . $id);
        }
    }
    public function showOrder(int $id): void
    {
        $this->render("pages/order_show", [
            "title" => "Order #" . $id,
            "order" => $this->orders->getOrderDetails($id),
            "is_admin" =>
                (bool) ($this->auth->getCurrentUser()["is_admin"] ?? false),
        ]);
    }
    public function history(): void
    {
        $this->render(
            "pages/orders",
            array_merge(
                ["title" => "Your orders"],
                $this->orders->listing(
                    $this->session->userId(),
                    max(1, (int) $this->request->get("page", 1)),
                ),
            ),
        );
    }
    public function updateStatus(int $id): void
    {
        $this->orders->updateStatus(
            $id,
            Validation::text($this->request->all(), "status", 20),
        );
        if (wants_json()) {
            $this->json([
                "success" => true,
                "message" => "Order status updated.",
                "redirect" => url_path("admin/order/" . $id),
            ]);
        } else {
            $this->redirect("/admin/order/" . $id);
        }
    }
}
