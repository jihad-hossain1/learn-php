<?php

namespace App\DTOs;

readonly class AuthVerifyDTO
{
    public function __construct(
        public string $email,
        public string $otp
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: trim($data['email']),
            otp: trim($data['otp'])
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'otp' => $this->otp,
        ];
    }
}
