<?php

namespace App\Services;

use App\Exceptions\OrderValidationException;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\CurrencyConverterResolverInterface;
use App\DTOs\OrderData;
use Symfony\Component\HttpFoundation\Response;


class OrderService implements OrderServiceInterface
{
    protected CurrencyConverterResolverInterface $converterResolver;

    public function __construct(CurrencyConverterResolverInterface $converterResolver)
    {
        $this->converterResolver = $converterResolver;
    }

    public function processOrder(OrderData $orderData): array
    {
        $this->validateName($orderData->name);
        $this->validateCurrency($orderData->currency);

        $converter = $this->converterResolver->resolve($orderData->currency);
        $convertedPrice = $converter->convertToTwd($orderData->price);

        $this->validatePrice($convertedPrice);

        // Update price and currency
        $result = [
            'id' => $orderData->id,
            'name' => $orderData->name,
            'address' => [
                'city' => $orderData->city,
                'district' => $orderData->district,
                'street' => $orderData->street,
            ],
            'price' => $convertedPrice,
            'currency' => 'TWD',
        ];

        return $result;
    }

    private function validateName(string $name): void
    {
        // Check if it contains non-English characters
        if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
            throw new OrderValidationException('Name contains non - English characters', Response::HTTP_BAD_REQUEST);
        }

        // Check if the first letter is capitalized
        if (!preg_match('/^[A-Z]/', $name)) {
            throw new OrderValidationException('Name is not capitalized', Response::HTTP_BAD_REQUEST);
        }
    }

    private function validatePrice(float $price): void
    {
        if ($price > 2000) {
            throw new OrderValidationException('The price cannot exceed 2000.', Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateCurrency(string $currency): void
    {
        $allowed = ['TWD', 'USD'];
        if (!in_array($currency, $allowed)) {
            throw new OrderValidationException('The currency must be either TWD or USD.', Response::HTTP_BAD_REQUEST);
        }
    }
}
