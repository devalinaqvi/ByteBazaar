<?php
class SecurityTest extends StoreTestCase
{
    public function testLoginLimiterAppliesAcrossSessions(): void
    {
        $limiter = new App\Services\LoginLimiter($this->db);
        for ($i = 0; $i < 10; $i++) {
            $limiter->attempt("customer@example.test", "127.0.0.1");
        }
        $anotherRequest = new App\Services\LoginLimiter($this->db);
        $this->expectException(App\Core\HttpException::class);
        $anotherRequest->attempt("customer@example.test", "127.0.0.2");
    }
    public function testRegistrationValidationAndPasswordWhitespace(): void
    {
        $state = [];
        $s = $this->services($state);
        $s["auth"]->register([
            "name" => "New Customer",
            "email" => "new@example.test",
            "password" => "  a-long-passphrase  ",
            "c-password" => "  a-long-passphrase  ",
        ]);
        $this->assertNotNull(
            $s["auth"]->authenticate(
                "new@example.test",
                "  a-long-passphrase  ",
            ),
        );
        $this->assertNull(
            $s["auth"]->authenticate("new@example.test", "a-long-passphrase"),
        );
        $this->expectException(App\Core\HttpException::class);
        $s["auth"]->register([
            "name" => "Bad",
            "email" => "invalid",
            "password" => "short",
            "c-password" => "mismatch",
        ]);
    }
    public function testPriceArithmeticUsesCentsAndRejectsInvalidAmounts(): void
    {
        $this->assertSame(101, App\Services\Pricing::cents("1.01"));
        $this->assertSame(10, App\Services\Pricing::cents("0.1"));
        $this->assertEquals(
            5.32,
            App\Services\Pricing::totals([
                ["price" => "0.10", "quantity" => 3],
            ])["total"],
        );
        $this->expectException(App\Core\HttpException::class);
        App\Services\Pricing::cents("-1.00");
    }
    public function testContainerFactoriesAndSingletonsHaveDistinctLifetimes(): void
    {
        $container = new App\Core\Container($this->db);
        $count = 0;
        $container->singleton("shared", function () use (&$count) {
            $count++;
            return new stdClass();
        });
        $container->bind("transient", fn() => new stdClass());
        $this->assertSame(0, $count);
        $this->assertSame($container->get("shared"), $container->get("shared"));
        $this->assertSame(1, $count);
        $this->assertNotSame(
            $container->get("transient"),
            $container->get("transient"),
        );
    }
}
