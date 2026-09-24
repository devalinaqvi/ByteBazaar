<?php
namespace App\Core;

/** Request-scoped session state. Never store an identity in a static property. */
final class SessionContext
{
    private array $data;
    public function __construct(array &$data)
    {
        $this->data = &$data;
    }
    public function userId(): ?int
    {
        return isset($this->data["user_id"])
            ? (int) $this->data["user_id"]
            : null;
    }
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
    public function cartKey(): string
    {
        if ($this->userId()) {
            return "user:" . $this->userId();
        }
        // Preserve legacy guest carts while moving account carts to stable ownership.
        return $this->data["session_id"] ??= bin2hex(random_bytes(32));
    }
    public function csrfToken(): string
    {
        return $this->data["csrf_token"] ??= bin2hex(random_bytes(32));
    }
    public function validCsrf(mixed $token): bool
    {
        return is_string($token) && hash_equals($this->csrfToken(), $token);
    }
    public function authenticate(int $id): void
    {
        $this->data = [
            "user_id" => $id,
            "csrf_token" => bin2hex(random_bytes(32)),
        ];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
    public function logout(): void
    {
        $this->data = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
