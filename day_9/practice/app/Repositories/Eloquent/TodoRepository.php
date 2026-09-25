<?php

namespace App\Repositories\Eloquent;

use App\DTOs\TodoDTO;
use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TodoRepository implements TodoRepositoryInterface
{
    public function __construct(
        private Todo $model
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(array $filters = []): Collection
    {
        return $this->model
            ->query()
            ->filter($filters)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Todo
    {
        return $this->model->query()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(int $id): Todo
    {
        return $this->model->query()->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(TodoDTO $dto): Todo
    {
        return $this->model->query()->create($dto->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, TodoDTO $dto): Todo
    {
        $todo = $this->findOrFail($id);
        $todo->update($dto->toArray());

        return $todo->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $todo = $this->findOrFail($id);

        return (bool) $todo->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function toggleComplete(int $id): Todo
    {
        $todo = $this->findOrFail($id);
        $todo->is_completed = ! $todo->is_completed;
        $todo->save();

        return $todo;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatistics(): array
    {
        $total = $this->model->query()->count();
        $completed = $this->model->query()->completed()->count();
        $pending = $total - $completed;
        $rate = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'completion_rate' => $rate,
        ];
    }
}
