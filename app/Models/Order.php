<?php

namespace App\Models;

class Order
{
    public int $id;
    public int $user_id;
    public float $total_price;
    public string $order_date;
    public string $status;

    public function __construct(int $id, int $user_id, float $total_price, string $order_date, string $status) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->total_price = $total_price;
        $this->order_date = $order_date;
        $this->status = $status;
    }
}
