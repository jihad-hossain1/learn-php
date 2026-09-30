<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\AuthRegisterDTO;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'email' => ['required', 'string', 'min:10', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:100']
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'user email',
            'password' => 'user password'
        ];
    }

    public function toDTO(): AuthRegisterDTO
    {
        return AuthRegisterDTO::fromArray($this->validated());
    }
}
