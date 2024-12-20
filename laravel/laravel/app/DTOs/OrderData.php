<?php

namespace App\DTOs;

class OrderData
{
    public string $id;
    public string $name;
    public string $city;
    public string $district;
    public string $street;
    public float $price;
    public string $currency;

    public function __construct(
        string $id,
        string $name,
        string $city,
        string $district,
        string $street,
        float $price,
        string $currency
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
        $this->district = $district;
        $this->street = $street;
        $this->price = $price;
        $this->currency = $currency;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['address']['city'],
            $data['address']['district'],
            $data['address']['street'],
            floatval($data['price']),
            $data['currency']
        );
    }
}
