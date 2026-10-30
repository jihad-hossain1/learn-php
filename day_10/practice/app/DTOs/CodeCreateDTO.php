<?php

namespace App\DTOs;

readonly class CodeCreateDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public string $description
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            type: trim($data['type']),
            description: trim($data['description'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description
        ];
    }
}
