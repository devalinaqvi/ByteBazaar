<?php

namespace App\Models;

class CartItem
{
    public ?int $id;

    public ?int $user_id;
    public ?string $session_id;

    public int $product_id;

    public int $quantity;
    public string $created_at;
    public string $updated_at;

    public ?object $product = null;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->session_id = $data['session_id'] ?? null;
        $this->product_id = $data['product_id'] ?? 0;
        $this->quantity = $data['quantity'] ?? 0;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        $this->product = null;
    }
}