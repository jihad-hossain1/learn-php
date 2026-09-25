<?php

namespace App\Models;

use Database\Factories\TodoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /** @use HasFactory<TodoFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'priority',
        'due_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'due_date' => 'date',
        ];
    }

    /**
     * @param  Builder<Todo>  $query
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    /**
     * @param  Builder<Todo>  $query
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    /**
     * @param  Builder<Todo>  $query
     * @param  array{status?: ?string, priority?: ?string, search?: ?string}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(($filters['status'] ?? null) === 'completed', fn (Builder $q) => $q->where('is_completed', true))
            ->when(($filters['status'] ?? null) === 'active', fn (Builder $q) => $q->where('is_completed', false))
            ->when(! empty($filters['priority']), fn (Builder $q) => $q->where('priority', $filters['priority']))
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $term = '%'.$filters['search'].'%';
                $q->where(fn (Builder $inner) => $inner->where('title', 'like', $term)->orWhere('description', 'like', $term));
            });
    }
}
