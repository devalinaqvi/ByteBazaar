<?php
class ProductRepositoryTest extends StoreTestCase
{
    public function testSoftDeleteExcludedFromSearchAndSlug(): void
    {
        $r = new App\Repositories\ProductRepository($this->db);
        $r->softDelete(1);
        $this->assertNull($r->findBySlug("laptop"));
        $this->assertSame(0, $r->catalog("Laptop", [1])["total"]);
    }
    public function testPaginationAndPriceSort(): void
    {
        $r = new App\Repositories\ProductRepository($this->db);
        $result = $r->catalog("", [], 1, "price_desc", 1);
        $this->assertSame(2, $result["total"]);
        $this->assertSame("Desktop", $result["products"][0]->name);
    }
    public function testMigrationCanRunTwice(): void
    {
        migrate($this->db);
        migrate($this->db);
        $this->assertEquals(
            2,
            $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        );
    }
}
