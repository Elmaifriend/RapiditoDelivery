<?php

namespace App\Services;

use App\Models\Cart;

class OrderLimitValidationService
{
    /**
     * Límite máximo de compra por defecto en MXN.
     */
    public const DEFAULT_MAX_AMOUNT = 500.00;

    /**
     * Determina si el total/subtotal del carrito excede el límite máximo permitido.
     *
     * @param Cart $cart
     * @param float $maxAmount Límite a comparar (por defecto 500)
     * @param bool $checkSubtotalOnly Si es true, valida solo productos. Si es false, valida el total con envío.
     */
    public function exceedsLimit(Cart $cart, float $maxAmount = self::DEFAULT_MAX_AMOUNT, bool $checkSubtotalOnly = true): bool
    {
        $amountToCheck = $checkSubtotalOnly ? $cart->subtotal : $cart->total;

        return $amountToCheck > $maxAmount;
    }

    /**
     * Retorna el monto límite por defecto.
     */
    public function getMaxAmount(): float
    {
        return self::DEFAULT_MAX_AMOUNT;
    }
}