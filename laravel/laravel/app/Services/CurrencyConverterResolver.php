<?php

namespace App\Services;

use App\Services\Contracts\CurrencyConverterResolverInterface;
use App\Services\Contracts\CurrencyConverterInterface;
use App\Services\CurrencyConverters\UsdToTwdConverter;
use App\Services\CurrencyConverters\TwdToTwdConverter;
use App\Exceptions\OrderValidationException;
use Symfony\Component\HttpFoundation\Response;

class CurrencyConverterResolver implements CurrencyConverterResolverInterface
{
    protected array $converters = [];

    public function __construct()
    {
        // Map currency pairs to the corresponding converter class names
        $this->converters = [
            'USD-TWD' => UsdToTwdConverter::class,
            'TWD-TWD' => TwdToTwdConverter::class,
            // If 'TWD-USD' is added in the future, simply add one line here
            // 'TWD-USD' => TwdToUsdConverter::class,
        ];
    }

    /**
     * Design concept: The exchange rate between USD and TWD is not
     * simply based on fixed multiplication or division.
     */
    public function resolve(string $fromCurrency, string $toCurrency): CurrencyConverterInterface
    {
        $key = "{$fromCurrency}-{$toCurrency}";

        if (isset($this->converters[$key])) {
            $converterClass = $this->converters[$key];
            return new $converterClass();
        }

        throw new OrderValidationException('Unsupported currency.', Response::HTTP_BAD_REQUEST);
    }
}
