<?php
namespace App\Repositories;

use App\Models\User;
use PDO;
use PDOException;

class UserRepository
{
    public function __construct(private readonly PDO $pdo) {}

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
}
