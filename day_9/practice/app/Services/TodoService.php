<?php

namespace App\Services;

use App\DTOs\TodoDTO;
use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TodoService
{
    /**
     * Inject the repository contract via the service container.
     */
    public function __construct(
        private TodoRepositoryInterface $todoRepository
    ) {}

    /**
     * Retrieve all todos matching filters.
     *
     * @param  array{status?: ?string, priority?: ?string, search?: ?string}  $filters
     * @return Collection<int, Todo>
     */
    public function listTodos(array $filters = []): Collection
    {
        return $this->todoRepository->all($filters);
    }

    /**
     * Get a single todo by ID.
     */
    public function getTodo(int $id): Todo
    {
        return $this->todoRepository->findOrFail($id);
    }

    /**
     * Create a new todo item from the DTO.
     */
    public function createTodo(TodoDTO $dto): Todo
    {
        $todo = $this->todoRepository->create($dto);

        Log::info('Todo item created successfully', [
            'id' => $todo->id,
            'title' => $todo->title,
            'priority' => $todo->priority,
        ]);

        return $todo;
    }

    /**
     * Update an existing todo item using the DTO.
     */
    public function updateTodo(int $id, TodoDTO $dto): Todo
    {
        $todo = $this->todoRepository->update($id, $dto);

        Log::info('Todo item updated successfully', [
            'id' => $todo->id,
            'title' => $todo->title,
            'is_completed' => $todo->is_completed,
        ]);

        return $todo;
    }

    /**
     * Delete a todo item.
     */
    public function deleteTodo(int $id): bool
    {
        $result = $this->todoRepository->delete($id);

        Log::info('Todo item deleted', ['id' => $id]);

        return $result;
    }

    /**
     * Toggle the completion status of a todo.
     */
    public function toggleTodoStatus(int $id): Todo
    {
        $todo = $this->todoRepository->toggleComplete($id);

        Log::info('Todo completion status toggled', [
            'id' => $todo->id,
            'is_completed' => $todo->is_completed,
        ]);

        return $todo;
    }

    /**
     * Get aggregated metrics/statistics for todos.
     *
     * @return array{total: int, completed: int, pending: int, completion_rate: int}
     */
    public function getStatistics(): array
    {
        return $this->todoRepository->getStatistics();
    }
}
