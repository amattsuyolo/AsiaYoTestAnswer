<?php

namespace App\Services\CurrencyConverters;

use App\Services\Contracts\CurrencyConverterInterface;

class UsdCurrencyConverter implements CurrencyConverterInterface
{
    protected float $exchangeRate;

    public function __construct()
    {
        // Assume the exchange rate is 1 USD = 31 TWD
        // (can be modified to fetch from a configuration file or an external API)
        $this->exchangeRate = 31.0;
    }

    public function convertToTwd(float $price): float
    {
        return $price * $this->exchangeRate;
    }
}
