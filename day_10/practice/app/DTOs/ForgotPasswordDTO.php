<?php

namespace App\DTOs;

readonly class ForgotPasswordDTO
{
    public function __construct(
        public string $email
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: trim($data['email'])
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
