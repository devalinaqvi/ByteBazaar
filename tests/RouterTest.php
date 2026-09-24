<?php
class RouterProbe
{
    public int $calls = 0;
    public function index(): void
    {
        $this->calls++;
    }
}
class RouterTest extends StoreTestCase
{
    public function testControllerCalledOnlyOnceAndCsrfRejectsMissingToken(): void
    {
        $state = [];
        $s = $this->services($state);
        $c = new App\Core\Container($this->db);
        $c->singleton("session", $s["session"]);
        $c->singleton("authService", $s["auth"]);
        $probe = new RouterProbe();
        $c->bind("\\App\\Controllers\\Probe", $probe);
        $router = new App\Core\Router($c);
        $router->get("/probe", "Probe@index", "public_probe");
        $router->post("/probe", "Probe@index", "public_probe_post");
        $router->dispatch("/probe", "GET");
        $this->assertSame(1, $probe->calls);
        ob_start();
        $router->dispatch("/probe", "POST");
        $response = json_decode(ob_get_clean(), true);
        $this->assertFalse($response["success"]);
        $this->assertSame(1, $probe->calls);
        $_POST["csrf_token"] = $s["session"]->csrfToken();
        $router->dispatch("/probe", "POST");
        $this->assertSame(2, $probe->calls);
    }
    public function testAllRegisteredRouteTargetsExist(): void
    {
        $router = new class {
            public array $targets = [];
            public function get($p, $t, $n)
            {
                $this->targets[] = $t;
            }
            public function post($p, $t, $n)
            {
                $this->targets[] = $t;
            }
        };
        require dirname(__DIR__) . "/routes/web.php";
        require dirname(__DIR__) . "/routes/admin.php";
        require dirname(__DIR__) . "/routes/user.php";
        foreach ($router->targets as $target) {
            [$class, $method] = explode("@", $target);
            $this->assertTrue(
                method_exists("App\\Controllers\\" . $class, $method),
                $target,
            );
        }
    }
}
