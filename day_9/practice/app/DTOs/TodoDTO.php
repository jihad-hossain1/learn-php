<?php

namespace App\DTOs;

readonly class TodoDTO
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public bool $isCompleted = false,
        public string $priority = 'medium',
        public ?string $dueDate = null,
    ) {}

    /**
     * Create a DTO instance from an associative array of data.
     *
     * @param array{
     *     title: string,
     *     description?: ?string,
     *     is_completed?: bool|int|string|null,
     *     priority?: ?string,
     *     due_date?: ?string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: trim($data['title']),
            description: isset($data['description']) && trim($data['description']) !== ''
                ? trim($data['description'])
                : null,
            isCompleted: filter_var($data['is_completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            priority: in_array($data['priority'] ?? 'medium', ['low', 'medium', 'high'], true)
                ? $data['priority']
                : 'medium',
            dueDate: ! empty($data['due_date']) ? $data['due_date'] : null,
        );
    }

    /**
     * Convert the DTO to an array matching the Eloquent model attributes.
     *
     * @return array{
     *     title: string,
     *     description: ?string,
     *     is_completed: bool,
     *     priority: string,
     *     due_date: ?string,
     * }
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'is_completed' => $this->isCompleted,
            'priority' => $this->priority,
            'due_date' => $this->dueDate,
        ];
    }
}
