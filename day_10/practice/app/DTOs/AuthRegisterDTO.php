<?php

namespace App\DTOs;

readonly class AuthRegisterDTO
{

    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            email: trim($data['email']),
            password: trim($data['password'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
