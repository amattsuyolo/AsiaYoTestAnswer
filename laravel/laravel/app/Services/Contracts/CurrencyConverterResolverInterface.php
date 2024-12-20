<?php

namespace App\Services\Contracts;

interface CurrencyConverterResolverInterface
{
    public function resolve(string $currency): CurrencyConverterInterface;
}
