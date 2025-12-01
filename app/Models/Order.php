<?php

namespace App\Models;

class Order
{
    public ?int $id = null;
    public ?int $user_id = null;
    public float $total_price;
    public string $status = 'pending';
    public ?string $order_date = null;
    public ?string $customer_name = null;
    public ?string $customer_email = null;
    public ?string $shipping_address = null;
    public ?string $shipping_city = null;
    public ?string $shipping_postal_code = null;
    public ?string $shipping_country = null;
    public ?string $phone = null;
    public string $payment_method = 'cash_on_delivery';

    // Relations
    public array $items = [];

}
