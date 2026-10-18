<?php

namespace App\Http\Requests;

use App\DTOs\AuthVerifyDTO;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string', 'digits:6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'user email',
            'otp' => 'OTP code',
        ];
    }

    public function toDTO(): AuthVerifyDTO
    {
        return AuthVerifyDTO::fromArray($this->validated());
    }
}
