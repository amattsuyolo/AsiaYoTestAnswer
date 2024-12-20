<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Services\Contracts\OrderServiceInterface;
use App\DTOs\OrderData;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    private OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function validateAndTransform(OrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $orderDataDto = OrderData::fromArray($validated);

        $result = $this->orderService->processOrder($orderDataDto);

        return response()->json($result, 200);
    }
}
