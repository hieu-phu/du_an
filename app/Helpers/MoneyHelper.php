<?php

namespace App\Helpers;

class MoneyHelper
{
    /**
     * Xóa định dạng tiền, chỉ giữ lại số.
     */
    public static function strip(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        return preg_replace('/[^0-9]/', '', (string) $value);
    }

    /**
     * Tính tổng tiền (bao gồm VAT và chiết khấu).
     */
    public static function calculateAmount(
        float|int $price,
        float|int $quantity,
        float|int $vat,
        float|int $discount
    ): array {
        $totalAmount = ($quantity * $price) - $discount;
        $totalVat = $totalAmount * ($vat / 100);
        $amount = $totalAmount + $totalVat;

        return compact('totalAmount', 'totalVat', 'amount');
    }
}
