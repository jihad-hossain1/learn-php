<?php

namespace App\Http\Requests;

use App\DTOs\AuthRegisterDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'user email',
            'password' => 'user password',
        ];
    }

    public function toDTO(): AuthRegisterDTO
    {
        return AuthRegisterDTO::fromArray($this->validated());
    }
}
