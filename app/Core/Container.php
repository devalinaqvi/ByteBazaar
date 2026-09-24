<?php
namespace App\Core;
use PDO;
/** A new container is built for each HTTP request, never shared between workers. */
class Container
{
    private array $bindings = [];
    private array $instances = [];
    public function __construct(PDO $pdo)
    {
        $this->instances = ["pdo" => $pdo, "db" => $pdo];
    }
    public function bind(string $key, callable|object $concrete): self
    {
        $this->bindings[$key] = [$concrete, false];
        unset($this->instances[$key]);
        return $this;
    }
    public function singleton(string $key, callable|object $concrete): self
    {
        $this->bindings[$key] = [$concrete, true];
        unset($this->instances[$key]);
        return $this;
    }
    public function get(string $key): mixed
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }
        if (!isset($this->bindings[$key])) {
            throw new \RuntimeException("No binding for {$key}");
        }
        [$factory, $shared] = $this->bindings[$key];
        $instance = is_callable($factory) ? $factory($this) : $factory;
        if ($shared) {
            $this->instances[$key] = $instance;
        }
        return $instance;
    }
}
