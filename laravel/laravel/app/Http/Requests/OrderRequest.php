<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'name' => 'required|string',
            'address.city' => 'required|string',
            'address.district' => 'required|string',
            'address.street' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'required|string',
        ];
    }
}
