<?php
namespace App\Controllers;
use App\Core\{BaseController, Request, SessionContext};
use App\Services\{OrderService, AuthService};
class UserController extends BaseController
{
    public function __construct(
        private OrderService $orders,
        private Request $request,
        private SessionContext $session,
        private AuthService $auth,
    ) {}
    public function dashboard(): void
    {
        $this->render(
            "pages/orders",
            array_merge(
                [
                    "title" => "Your account",
                    "customer" => $this->auth->getCurrentUser(),
                ],
                $this->orders->listing(
                    $this->session->userId(),
                    max(1, (int) $this->request->get("page", 1)),
                ),
            ),
        );
    }
}
