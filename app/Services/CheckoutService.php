<?php
namespace App\Services;
use App\Core\{SessionContext, Validation, HttpException};
use App\Repositories\OrderRepository;
class CheckoutService
{
    public function __construct(
        private OrderRepository $orders,
        private SessionContext $session,
    ) {}
    public function token(): string
    {
        $key = $this->session->get("checkout_token");
        if (!$key) {
            $key = bin2hex(random_bytes(32));
            $this->session->set("checkout_token", $key);
        }
        return $key;
    }
    public function place(array $data): int
    {
        $key = $data["checkout_key"] ?? "";
        $tokens = $this->session->get("completed_checkouts", []);
        if (
            !is_string($key) ||
            !preg_match('/^[a-f0-9]{64}$/', $key) ||
            ($key !== $this->session->get("checkout_token") &&
                !isset($tokens[$key]))
        ) {
            throw new HttpException(
                403,
                "Please reload checkout before placing your order.",
            );
        }
        if (isset($tokens[$key])) {
            return $tokens[$key];
        }
        $clean = [
            "customer_email" => Validation::email($data, "customer_email"),
        ];
        foreach (
            [
                "customer_name" => 100,
                "shipping_address" => 500,
                "shipping_city" => 100,
                "shipping_country" => 100,
                "shipping_postal_code" => 20,
                "phone" => 30,
            ]
            as $field => $length
        ) {
            $clean[$field] = Validation::text($data, $field, $length);
        }
        if (($data["payment_method"] ?? "") !== "cash_on_delivery") {
            throw new HttpException(422, "Only cash on delivery is available.");
        }
        $clean["payment_method"] = "cash_on_delivery";
        $id = $this->orders->place(
            $this->session->cartKey(),
            $this->session->userId(),
            $clean,
            $key,
        );
        $tokens[$key] = $id;
        $this->session->set(
            "completed_checkouts",
            array_slice($tokens, -20, null, true),
        );
        $this->session->set("checkout_token", null);
        return $id;
    }
}
