<?php

namespace App\Services\Contracts;

use App\DTOs\OrderData;

interface OrderServiceInterface
{
    /**
     * Process the order data: check price limit, currency conversion, etc.
     *
     * @param OrderData $orderData
     * @return array processed order data
     * @throws \Exception on validation/conversion errors
     */
    public function processOrder(OrderData $orderData): array;
}
