<?php

namespace App\Services\Contracts;

interface CurrencyConverterInterface
{
    /**
     * Convert given price to TWD
     *
     * @param float $price
     * @return float converted price in TWD
     */
    public function convertToTwd(float $price): float;
}
