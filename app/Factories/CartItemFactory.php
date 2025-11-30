<?php

namespace App\Factories;

use App\Models\CartItem;

class CartItemFactory
{
    public static function fromArray(array $data): CartItem
    {
        // Use the constructor instead of bypassing it
        return new CartItem($data);
    }
}