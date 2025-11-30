<?php

namespace App\Factories;

use App\Models\Product;

class ProductFactory
{
    public static function fromArray(array $data): Product
    {
        $product = new Product();

        foreach ($data as $key => $value) {
            if (property_exists($product, $key)) {
                $product->$key = $value;
            }
        }

        return $product;
    }
}
