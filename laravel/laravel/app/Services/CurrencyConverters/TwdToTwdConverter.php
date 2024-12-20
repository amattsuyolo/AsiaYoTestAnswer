<?php

namespace App\Services\CurrencyConverters;

use App\Services\Contracts\CurrencyConverterInterface;

class TwdToTwdConverter implements CurrencyConverterInterface
{
    protected float $exchangeRate;

    public function __construct()
    {
        $this->exchangeRate = 1.0; // TWD to TWD exchange rate is always 1
    }

    public function convert(float $amount): float
    {
        // Directly return the same amount since TWD to TWD conversion is 1:1
        return $amount * $this->exchangeRate;
    }
}
