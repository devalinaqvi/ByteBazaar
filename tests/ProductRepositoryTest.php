<?php

use App\Repositories\ProductRepository;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    private PDO $db;
    private ProductRepository $repo;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("
            CREATE TABLE products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                slug TEXT,
                description TEXT,
                price REAL,
                stock INTEGER,
                category_id INTEGER,
                image TEXT,
                created_at TEXT,
                updated_at TEXT,
                deleted_at TEXT
            );
        ");

        $this->repo = new ProductRepository($this->db);
    }

    public function testCreateProduct()
    {
        $id = $this->repo->create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'desc',
            'price' => 999.90,
            'stock' => 10,
            'category_id' => 1,
            'image' => '/uploads/test.jpg',
        ]);

        $this->assertEquals(1, $id);
    }

    public function testSoftDelete()
    {
        $id = $this->repo->create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'desc',
            'price' => 999.90,
            'stock' => 10,
            'category_id' => 1,
            'image' => '/uploads/test.jpg',
        ]);

        $this->repo->softDelete($id);

        $product = $this->repo->find($id);

        $this->assertNull($product);
    }
}