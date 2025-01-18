<?php

namespace App\Helpers;
class Helpers
{
    public static function calcTotalPrice(array $orders): float
    {
        $total = 0;
        foreach ($orders as $order) {
            if (isset($order['price'], $order['quantity']) && is_numeric($order['price']) && is_numeric($order['quantity'])) {
                $total += $order['price'] * $order['quantity'];
            }
        }
        return $total;
    }
}