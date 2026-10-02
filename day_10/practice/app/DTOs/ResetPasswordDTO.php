<?php

namespace App\DTOs;

readonly class ResetPasswordDTO
{
    public function __construct(
        public string $email,
        public string $otp,
        public string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: trim($data['email']),
            otp: trim($data['otp']),
            password: trim($data['password'])
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'otp' => $this->otp,
            'password' => $this->password,
        ];
    }
}
