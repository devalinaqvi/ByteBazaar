<?php
namespace App\Services;
use App\Repositories\OrderRepository;
use App\Core\{HttpException, SessionContext};
class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private AuthService $auth,
        private SessionContext $session,
    ) {}
    public function listing(?int $userId = null, int $page = 1): array
    {
        return $this->orders->listing($userId, $page);
    }
    public function listAllOrders(): array
    {
        return $this->orders->getAll();
    }
    public function getOrderDetails(int $id): array
    {
        $order = $this->orders->find($id);
        $user = $this->auth->getCurrentUser();
        $owns =
            $order &&
            (($user &&
                $order["user_id"] !== null &&
                (int) $order["user_id"] === $user["id"]) ||
                (!$user &&
                    $order["user_id"] === null &&
                    in_array(
                        $id,
                        $this->session->get("completed_checkouts", []),
                        true,
                    )));
        if (!$order || (!$owns && !($user["is_admin"] ?? false))) {
            throw new HttpException(404, "Order not found.");
        }
        return $order;
    }
    public function updateStatus(int $id, string $status): void
    {
        if (!($this->auth->getCurrentUser()["is_admin"] ?? false)) {
            throw new HttpException(403, "Administrator access required.");
        }
        $this->orders->updateStatus($id, $status);
    }
}
