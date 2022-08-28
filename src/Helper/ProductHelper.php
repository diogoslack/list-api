<?php
namespace App\Helper;

class ProductHelper {

    public static function getValuesFromString(string $value): array
    {
        preg_match('/(\d+|\D+)(.*)/', $value, $matches);
        if ($matches && $matches[1] && $matches[2]) {
            $price = (float) $matches[2];
            $currency = trim($matches[1]);           
            return ['price' => $price, 'currency' => $currency];
        }
        return [];
    }
}