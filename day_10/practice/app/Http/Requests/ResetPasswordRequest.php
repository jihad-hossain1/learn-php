<?php

namespace App\Http\Requests;

use App\DTOs\ResetPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'digits:6'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'user email',
            'otp' => 'OTP code',
            'password' => 'new password',
        ];
    }

    public function toDTO(): ResetPasswordDTO
    {
        return ResetPasswordDTO::fromArray($this->validated());
    }
}
