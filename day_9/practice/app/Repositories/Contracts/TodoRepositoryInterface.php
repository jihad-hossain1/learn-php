<?php

namespace App\Repositories\Contracts;

use App\DTOs\TodoDTO;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

interface TodoRepositoryInterface
{
    /**
     * Retrieve all todos matching optional filters.
     *
     * @param  array{status?: ?string, priority?: ?string, search?: ?string}  $filters
     * @return Collection<int, Todo>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a todo by its ID.
     */
    public function findById(int $id): ?Todo;

    /**
     * Find a todo by its ID or throw a ModelNotFoundException.
     */
    public function findOrFail(int $id): Todo;

    /**
     * Create a new todo record from a DTO.
     */
    public function create(TodoDTO $dto): Todo;

    /**
     * Update an existing todo record with new DTO data.
     */
    public function update(int $id, TodoDTO $dto): Todo;

    /**
     * Delete a todo record by its ID.
     */
    public function delete(int $id): bool;

    /**
     * Toggle the completion status of a todo.
     */
    public function toggleComplete(int $id): Todo;

    /**
     * Retrieve statistics about todos.
     *
     * @return array{total: int, completed: int, pending: int, completion_rate: int}
     */
    public function getStatistics(): array;
}
