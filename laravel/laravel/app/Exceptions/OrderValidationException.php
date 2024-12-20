<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class OrderValidationException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = "", int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 在例外中直接定義回應格式
     * Laravel 會在拋出此例外時自動使用此方法產生 HTTP 回應。
     */
    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getStatusCode());
    }
}
