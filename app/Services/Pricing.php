<?php
namespace App\Services;
final class Pricing
{
    public static function cents(string|int|float $value): int
    {
        $decimal = (string) $value;
        if (!preg_match('/^([0-9]+)(?:\.([0-9]{1,2}))?$/', $decimal, $parts)) {
            throw new \App\Core\HttpException(
                422,
                "A product has an invalid price. Please contact the store.",
            );
        }
        return (int) $parts[1] * 100 + (int) str_pad($parts[2] ?? "", 2, "0");
    }
    public static function totals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += self::cents($item["price"]) * (int) $item["quantity"];
        }
        $shipping = $items ? 500 : 0;
        $tax = intdiv($subtotal * 8 + 50, 100);
        if ($subtotal + $shipping + $tax > 9999999999) {
            throw new \App\Core\HttpException(
                422,
                "This order exceeds the supported total. Please reduce the quantities.",
            );
        }
        return [
            "subtotal" => $subtotal / 100,
            "shipping" => $shipping / 100,
            "tax" => $tax / 100,
            "total" => ($subtotal + $shipping + $tax) / 100,
        ];
    }
}
