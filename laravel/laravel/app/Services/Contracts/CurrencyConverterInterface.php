<?php

namespace App\Services\Contracts;

interface CurrencyConverterInterface
{
    /**
     * Convert a given amount from one currency to another
     *
     * @param float $amount
     */
    public function convert(float $amount): float;
}
