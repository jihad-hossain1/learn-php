<?php

namespace App\Http\Requests;

use App\DTOs\ForgotPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'user email',
        ];
    }

    public function toDTO(): ForgotPasswordDTO
    {
        return ForgotPasswordDTO::fromArray($this->validated());
    }
}
