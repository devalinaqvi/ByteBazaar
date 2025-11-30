<?php

namespace App\Models;

class Product
{
    public ?int $id = null;

    public string $name = '';

    public ?string $slug = null;

    public string $description = '';

    public float $price = 0.0;

    public int $stock = 0;

    public int $category_id = 0;

    public ?string $image_url = null;

    public string $created_at = '';

    public string $updated_at = '';

    public ?string $deleted_at = null;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->slug = $data['slug'] ?? null;
        $this->description = $data['description'] ?? '';
        $this->price = $data['price'] ?? 0.0;
        $this->stock = $data['stock'] ?? 0;
        $this->category_id = $data['category_id'] ?? 0;
        $this->image_url = $data['image_url'] ?? null;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        $this->deleted_at = $data['deleted_at'] ?? null;
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }
}