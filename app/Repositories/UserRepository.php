<?php
namespace App\Repositories;

use App\Models\User;
use PDO;

readonly class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
        );
        $stmt->execute(["email" => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row);
        }

        return null;
    }

    public function listing(int $page): array
    {
        $total = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM users")
            ->fetchColumn();
        $page = max(1, min($page, max(1, (int) ceil($total / 20))));
        $users = $this->pdo
            ->query(
                "SELECT id, name, email, created_at, is_admin FROM users ORDER BY id DESC LIMIT 20 OFFSET " .
                    ($page - 1) * 20,
            )
            ->fetchAll(PDO::FETCH_ASSOC);
        return [
            "users" => $users,
            "page" => $page,
            "pages" => max(1, (int) ceil($total / 20)),
            "total" => $total,
        ];
    }
    public function create(string $name, string $email, string $password): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :pass)
        ");

        $stmt->execute([
            "name" => $name,
            "email" => $email,
            "pass" => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = :id LIMIT 1",
        );
        $stmt->execute(["id" => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row);
        }
        return null;
    }
}
