<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\CodeCreateDTO;

class CodeCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'type' => ['required', 'string', 'min:5', 'max:100'],
            'description' => ['string', 'max:500']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'product name',
            'type' => 'product type',
            'description' => 'product description'
        ];
    }

    public function toDTO(): CodeCreateDTO
    {
        return CodeCreateDTO::fromArray($this->validated());
    }
}
