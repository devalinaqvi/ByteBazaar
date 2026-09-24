<?php
class CheckoutTest extends StoreTestCase
{
    public function testOrderStockCartTotalsAndIdempotency(): void
    {
        $state = ["user_id" => 1];
        $s = $this->services($state);
        $s["cart"]->addToCart(1, 2);
        $data = $this->address($s["checkout"]->token());
        $id = $s["checkout"]->place($data);
        $this->assertSame($id, $s["checkout"]->place($data));
        $order = $s["orders"]->getOrderDetails($id);
        $this->assertEquals(221.54, $order["total_price"]);
        $this->assertSame(0, $s["cart"]->count());
        $this->assertEquals(
            3,
            $this->db
                ->query("SELECT stock FROM products WHERE id=1")
                ->fetchColumn(),
        );
        $this->assertEquals(
            1,
            $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        );
    }
    public function testStockFailureRollsBackEntireOrder(): void
    {
        $state = [];
        $s = $this->services($state);
        $s["cart"]->addToCart(1, 2);
        $s["cart"]->addToCart(2, 2);
        $this->db->exec("UPDATE products SET stock=1 WHERE id=2");
        try {
            $s["checkout"]->place($this->address($s["checkout"]->token()));
            $this->fail("Oversell allowed");
        } catch (App\Core\HttpException $e) {
            $this->assertSame(409, $e->status);
        }
        $this->assertEquals(
            5,
            $this->db
                ->query("SELECT stock FROM products WHERE id=1")
                ->fetchColumn(),
        );
        $this->assertEquals(
            0,
            $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        );
        $this->assertSame(4, $s["cart"]->count());
    }
    public function testDeletedProductCannotBeOrdered(): void
    {
        $state = [];
        $s = $this->services($state);
        $s["cart"]->addToCart(1);
        $this->db->exec(
            "UPDATE products SET deleted_at=CURRENT_TIMESTAMP WHERE id=1",
        );
        $this->expectException(App\Core\HttpException::class);
        $s["checkout"]->place($this->address($s["checkout"]->token()));
    }
    public function testOtherCustomersCannotReadAnOrderButAdminCan(): void
    {
        $a = ["user_id" => 1];
        $b = ["user_id" => 2];
        $c = ["user_id" => 3];
        $one = $this->services($a);
        $two = $this->services($b);
        $admin = $this->services($c);
        $one["cart"]->addToCart(1);
        $id = $one["checkout"]->place(
            $this->address($one["checkout"]->token()),
        );
        $this->assertSame(
            $id,
            (int) $admin["orders"]->getOrderDetails($id)["id"],
        );
        $this->expectException(App\Core\HttpException::class);
        $two["orders"]->getOrderDetails($id);
    }
    public function testGuestReceiptOnlyVisibleInOriginalSession(): void
    {
        $a = [];
        $b = [];
        $one = $this->services($a);
        $two = $this->services($b);
        $one["cart"]->addToCart(1);
        $id = $one["checkout"]->place(
            $this->address($one["checkout"]->token()),
        );
        $this->assertSame(
            $id,
            (int) $one["orders"]->getOrderDetails($id)["id"],
        );
        $this->expectException(App\Core\HttpException::class);
        $two["orders"]->getOrderDetails($id);
    }
    public function testInvalidQuantitiesRejected(): void
    {
        $a = [];
        $s = $this->services($a);
        foreach ([-1, 0, 6, 1000] as $qty) {
            try {
                $s["cart"]->addToCart(1, $qty);
                $this->fail("Quantity accepted");
            } catch (App\Core\HttpException $e) {
                $this->assertSame(422, $e->status);
            }
        }
        $s["cart"]->addToCart(1, 4);
        $this->expectException(App\Core\HttpException::class);
        $s["cart"]->addToCart(1, 2);
    }
    public function testStatusTransitionsAreSequential(): void
    {
        $a = ["user_id" => 3];
        $s = $this->services($a);
        $s["cart"]->addToCart(1);
        $id = $s["checkout"]->place($this->address($s["checkout"]->token()));
        $s["orders"]->updateStatus($id, "processing");
        $this->assertSame(
            "processing",
            $s["orders"]->getOrderDetails($id)["status"],
        );
        $this->expectException(App\Core\HttpException::class);
        $s["orders"]->updateStatus($id, "delivered");
    }
}
