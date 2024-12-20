<?php

namespace App\Services\CurrencyConverters;

use App\Services\Contracts\CurrencyConverterInterface;

class TwdCurrencyConverter implements CurrencyConverterInterface
{
    public function convertToTwd(float $price): float
    {
        // For demonstration purposes only, no conversion is performed if the currency is TWD
        return $price;
    }
}
