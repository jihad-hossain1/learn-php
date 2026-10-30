<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\ProductCreateDTO;

class ProductCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'price' => ['required', 'int'],
            'description' => ['string', 'max:500']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'product name',
            'price' => 'product price',
            'description' => 'product description'
        ];
    }

    public function toDTO(): ProductCreateDTO
    {
        return ProductCreateDTO::fromArray($this->validated());
    }
}
