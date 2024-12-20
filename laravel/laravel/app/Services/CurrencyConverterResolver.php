<?php

namespace App\Services;

use App\Services\Contracts\CurrencyConverterResolverInterface;
use App\Services\Contracts\CurrencyConverterInterface;
use App\Services\CurrencyConverters\TwdCurrencyConverter;
use App\Services\CurrencyConverters\UsdCurrencyConverter;
use App\Exceptions\OrderValidationException;
use Symfony\Component\HttpFoundation\Response;

class CurrencyConverterResolver implements CurrencyConverterResolverInterface
{
    public function resolve(string $currency): CurrencyConverterInterface
    {
        return match ($currency) {
            'TWD' => new TwdCurrencyConverter(),
            'USD' => new UsdCurrencyConverter(),
            default => throw new OrderValidationException('Unsupported currency.', Response::HTTP_BAD_REQUEST)
        };
    }
}
