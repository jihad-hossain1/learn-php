<?php

namespace App\DTOs;

readonly class ProductCreateDTO
{
    public function __construct(
        public string $name,
        public int $price,
        public string $description
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            price: $data['price'],
            description: trim($data['description'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description
        ];
    }
}
