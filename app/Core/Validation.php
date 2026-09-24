<?php
namespace App\Core;
final class Validation
{
    public static function text(
        array $data,
        string $key,
        int $max = 255,
    ): string {
        $value = $data[$key] ?? "";
        if (
            !is_string($value) ||
            trim($value) === "" ||
            mb_strlen(trim($value)) > $max
        ) {
            throw new HttpException(
                422,
                ucfirst(str_replace("_", " ", $key)) .
                    " is required (maximum {$max} characters).",
            );
        }
        return trim($value);
    }
    public static function integer(
        mixed $value,
        int $min = 1,
        int $max = 999,
    ): int {
        $n = filter_var($value, FILTER_VALIDATE_INT);
        if ($n === false || $n < $min || $n > $max) {
            throw new HttpException(
                422,
                "Enter a whole number between {$min} and {$max}.",
            );
        }
        return $n;
    }
    public static function email(array $data, string $key = "email"): string
    {
        $email = strtolower(self::text($data, $key));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpException(422, "Enter a valid email address.");
        }
        return $email;
    }
}
