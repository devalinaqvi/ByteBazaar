<?php
namespace App\Core;

use PDO;  // For type hints

class Container {
    private array $bindings = [];
    private array $instances = [];

    public function __construct(PDO $pdo) {  // Accept PDO directly (your $pdo)
        $this->instances['pdo'] = $pdo;
        $this->bind('db', $pdo);  // Alias for legacy
    }

    public function bind(string $key, callable|object $concrete): self {
        $this->bindings[$key] = $concrete;
        unset($this->instances[$key]);  // Reset if singleton
        return $this;
    }

    public function singleton(string $key, callable|object $concrete): self {
        $instance = is_callable($concrete) ? $concrete($this) : $concrete;
        $this->instances[$key] = $instance;
        return $this;
    }

    public function get(string $key) {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            throw new \Exception("No binding for $key");
        }

        $concrete = $this->bindings[$key];
        $instance = is_callable($concrete) ? $concrete($this) : $concrete;  // Lazy resolve

        // Singleton pattern (cache instances)
        $this->instances[$key] = $instance;
        return $instance;
    }

    // Factory for controllers (inject deps via closure)
    public function make(string $class): object {
        if (!class_exists($class)) {
            throw new \Exception("Class $class not found");
        }
        return new $class($this);  // Pass container for constructor deps
    }
}