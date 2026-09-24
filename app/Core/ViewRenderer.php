<?php
namespace App\Core;
use App\Services\{AuthService, CartService};
final class ViewRenderer
{
    public function __construct(
        private AuthService $auth,
        private CartService $cart,
    ) {}
    public function render(string $view, array $data = []): void
    {
        $data["viewer"] = $this->auth->getCurrentUser();
        $data["bagCount"] = $this->cart->count();
        $data["view"] = $view;
        $data["csrf_token"] = csrf_hash();
        extract($data, EXTR_SKIP);
        require base_path("app/Views/layouts/main.php");
    }
}
