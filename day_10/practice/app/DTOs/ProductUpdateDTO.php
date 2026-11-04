<?php

namespace App\DTOs;

readonly class ProductUpdateDTO
{
    public function __construct(
        public string $name,
        public int $price,
        public string $description,
        public int $cat_id,
        public string $slug
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'],
            name: trim($data['name']),
            price: $data['price'],
            description: trim($data['description']),
            cat_id: $data['cat_id']
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'cat_id' => $this->cat_id
        ];
    }
}
