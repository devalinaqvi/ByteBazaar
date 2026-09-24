<?php
class SessionIsolationTest extends StoreTestCase
{
    public function testGuestCartsAndCsrfAreIsolated(): void
    {
        $a = [];
        $b = [];
        $one = $this->services($a);
        $two = $this->services($b);
        $one["cart"]->addToCart(1, 2);
        $two["cart"]->addToCart(2, 1);
        $this->assertNotSame(
            $one["session"]->cartKey(),
            $two["session"]->cartKey(),
        );
        $this->assertFalse(
            $two["session"]->validCsrf($one["session"]->csrfToken()),
        );
        $this->assertSame(1, (int) $one["cart"]->getCart()[0]["product_id"]);
        $this->assertSame(2, (int) $two["cart"]->getCart()[0]["product_id"]);
    }
    public function testCrossSessionCartUpdateAndDeleteAreDenied(): void
    {
        $a = [];
        $b = [];
        $one = $this->services($a);
        $two = $this->services($b);
        $one["cart"]->addToCart(1);
        $id = $one["cart"]->getCart()[0]["id"];
        foreach (["update", "remove"] as $method) {
            try {
                $method === "update"
                    ? $two["cart"]->update($id, 4)
                    : $two["cart"]->remove($id);
                $this->fail("Cross-session mutation was allowed");
            } catch (App\Core\HttpException $e) {
                $this->assertSame(404, $e->status);
            }
        }
        $this->assertSame(1, $one["cart"]->getCart()[0]["quantity"]);
    }
    public function testLoginMergeLogoutAndAccountSwitchDoNotLeak(): void
    {
        $state = [];
        $services = $this->services($state);
        $services["cart"]->addToCart(1, 2);
        $oldToken = $services["session"]->csrfToken();
        $users = new App\Repositories\UserRepository($this->db);
        $services["auth"]->login($users->findById(1));
        $this->assertSame("user:1", $services["session"]->cartKey());
        $this->assertFalse($services["session"]->validCsrf($oldToken));
        $other = ["user_id" => 1];
        $same = $this->services($other);
        $this->assertSame(2, $same["cart"]->count());
        $services["auth"]->logout();
        $this->assertSame(0, $services["cart"]->count());
        $services["cart"]->addToCart(2);
        $services["auth"]->login($users->findById(2));
        $this->assertSame(
            2,
            (int) $services["cart"]->getCart()[0]["product_id"],
        );
        $this->assertSame(2, $same["cart"]->count());
    }
    public function testSeparateContainersNeverShareSessionSingletons(): void
    {
        $a = ["user_id" => 1];
        $b = ["user_id" => 2];
        $one = new App\Core\Container($this->db);
        $two = new App\Core\Container($this->db);
        $one->singleton("session", new App\Core\SessionContext($a));
        $two->singleton("session", new App\Core\SessionContext($b));
        $this->assertNotSame($one->get("session"), $two->get("session"));
        $this->assertSame(1, $one->get("session")->userId());
        $this->assertSame(2, $two->get("session")->userId());
    }
    public function testRoleIsLoadedFromDatabaseNotSessionFlags(): void
    {
        $state = ["user_id" => 1, "is_admin" => true];
        $s = $this->services($state);
        $this->assertFalse($s["auth"]->getCurrentUser()["is_admin"]);
        $this->db->exec("UPDATE users SET is_admin=1 WHERE id=1");
        $this->assertTrue($s["auth"]->getCurrentUser()["is_admin"]);
    }
    public function testDatabaseAndInstallationNamespacesDoNotShareCookies(): void
    {
        $a = ["db" => ["host" => "localhost", "dbname" => "store"]];
        $b = ["db" => ["host" => "localhost", "dbname" => "preview"]];
        $this->assertNotSame(
            App\Core\Config::sessionName($a, "/store"),
            App\Core\Config::sessionName($b, "/store"),
        );
        $this->assertNotSame(
            App\Core\Config::sessionName($a, "/store"),
            App\Core\Config::sessionName($a, "/another-store"),
        );
    }
}
