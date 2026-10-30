<?php

namespace App\DTOs;

readonly class ProductCreateDTO
{
    public function __construct(
        public string $name,
        public int $price,
        public string $description,
        public int $cat_id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            price: $data['price'],
            description: trim($data['description']),
            cat_id: $data['cat_id']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'cat_id' => $this->cat_id
        ];
    }
}
