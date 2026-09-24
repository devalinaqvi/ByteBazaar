<?php
namespace App\Services;
use PDO;
use App\Core\HttpException;
/** Shared database counters prevent bypassing the limit by clearing cookies. */
class LoginLimiter
{
    public function __construct(private PDO $db) {}
    public function attempt(string $email, string $ip): void
    {
        $window = intdiv(time(), 900);
        foreach (
            ["email:" . strtolower($email) => 10, "ip:" . $ip => 50]
            as $identity => $limit
        ) {
            $key = hash("sha256", $identity . ":" . $window);
            $sql =
                $this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === "mysql"
                    ? "INSERT INTO login_attempts (attempt_key, attempts, expires_at) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE attempts = attempts + 1"
                    : "INSERT INTO login_attempts (attempt_key, attempts, expires_at) VALUES (?, 1, ?) ON CONFLICT(attempt_key) DO UPDATE SET attempts = attempts + 1";
            $s = $this->db->prepare($sql);
            $s->execute([$key, ($window + 1) * 900]);
            $s = $this->db->prepare(
                "SELECT attempts FROM login_attempts WHERE attempt_key = ?",
            );
            $s->execute([$key]);
            if ((int) $s->fetchColumn() > $limit) {
                throw new HttpException(
                    429,
                    "Too many sign-in attempts. Please try again in 15 minutes.",
                );
            }
        }
        $s = $this->db->prepare(
            "DELETE FROM login_attempts WHERE expires_at < ?",
        );
        $s->execute([time()]);
    }
}
