<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\ProductUpdateDTO;

class ProductUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'min:5'],
            'name' => ['string', 'max:255'],
            'price' => ['int'],
            'description' => ['string', 'max:500'],
            'cat_id' => ['int']
        ];
    }

    public function attributes()
    {
        return [
            'slug' => 'product slug',
            'name' => 'product name',
            'price' => 'product price',
            'cat_id' => 'product category',
            'description' => 'product description'
        ];
    }

    public function toDTO(): ProductUpdateDTO
    {
        return ProductUpdateDTO::fromArray($this->validated());
    }
}
