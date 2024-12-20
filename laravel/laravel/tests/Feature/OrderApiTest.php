<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderApiTest extends TestCase
{
    #[DataProvider('validOrderDataProvider')]
    public function test_it_processes_valid_orders(array $payload, array $expectedResponse)
    {
        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(200)
                 ->assertJson($expectedResponse);
    }

    public static function validOrderDataProvider(): array
    {
        return [
            'process order with TWD' => [
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 1000,
                    'currency' => 'TWD',
                ],
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 1000,
                    'currency' => 'TWD',
                ],
            ],
            'convert price when currency is USD' => [
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 10,
                    'currency' => 'USD',
                ],
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 310, // 假設匯率轉換後為 310
                    'currency' => 'TWD',
                ],
            ],
        ];
    }

    #[DataProvider('invalidOrderDataProvider')]
    public function test_it_fails_with_invalid_data(array $payload, string $expectedError)
    {
        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(400)
                 ->assertJson(['error' => $expectedError]);
    }

    public static function invalidOrderDataProvider(): array
    {
        return [
            'name contains non english characters' => [
                [
                    'id' => 'A0000001',
                    'name' => '麥特',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 1000,
                    'currency' => 'TWD',
                ],
                'Name contains non - English characters',
            ],
            'name does not start with uppercase' => [
                [
                    'id' => 'A0000001',
                    'name' => 'melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 1000,
                    'currency' => 'TWD',
                ],
                'Name is not capitalized',
            ],
            'price exceeds 2000' => [
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 3000,
                    'currency' => 'TWD',
                ],
                'The price cannot exceed 2000.',
            ],
            'unsupported currency' => [
                [
                    'id' => 'A0000001',
                    'name' => 'Melody Holiday Inn',
                    'address' => [
                        'city' => 'taipei-city',
                        'district' => 'da-an-district',
                        'street' => 'fuxing-south-road',
                    ],
                    'price' => 1000,
                    'currency' => 'EUR',
                ],
                'The currency must be either TWD or USD.',
            ],
        ];
    }
}
