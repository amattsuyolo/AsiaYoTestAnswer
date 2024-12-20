<?php

namespace App\Services\CurrencyConverters;

use App\Services\Contracts\CurrencyConverterInterface;

class UsdToTwdConverter implements CurrencyConverterInterface
{
    protected float $exchangeRate;

    public function __construct()
    {
        $this->exchangeRate = 31.0;
    }

    public function convert(float $amount): float
    {
        // No need to check the currency type as this converter specializes in USD→TWD
        return $amount * $this->exchangeRate;
    }
}
