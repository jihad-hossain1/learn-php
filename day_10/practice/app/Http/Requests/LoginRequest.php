<?php

namespace App\Http\Requests;

use App\DTOs\AuthLoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'user email',
            'password' => 'user password',
        ];
    }

    public function toDTO(): AuthLoginDTO
    {
        return AuthLoginDTO::fromArray($this->validated());
    }
}
