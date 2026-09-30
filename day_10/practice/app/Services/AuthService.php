<?php

namespace App\Services;

use App\DTOs\AuthRegisterDTO;

class AuthService
{
    public function register(AuthRegisterDTO $dto): array
    {
        return [
            'success' => true
        ];
    }
}
