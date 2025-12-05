<?php
namespace App\Repositories;

use App\Models\User;
use PDO;
use PDOException;

readonly class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?User
    {
        try {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            logMessage("Error setting PDO error mode: {$e->getMessage()}");
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            logMessage("Found user with email: {$email}");
            return new User($row);
        }
        logMessage("User not found with email: {$email}");
        return null;
    }

    public function listAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return new User($row);
        }, $rows);
    }

    public function create(string $name, string $email, string $password): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :pass)
        ");

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'pass' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row);
        }
        return null;
    }

    public function listAllOrders(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
