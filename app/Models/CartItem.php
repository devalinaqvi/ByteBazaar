<?php

namespace App\Models;

class CartItem
{
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->product_id = $data['product_id'] ?? null;
        $this->quantity = $data['quantity'] ?? null;
    }
}