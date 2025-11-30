<?php

namespace App\Models;

class OrderItem
{
    public ?int $id = null;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $unit_price;
    public ?string $product_name = null;
    public ?string $product_image = null;
}
