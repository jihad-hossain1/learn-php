<?php

namespace App\Services;

use App\Http\Data\Todo\CreateTodoData;
use App\Models\Todo;

class TodoService
{
    public function create(CreateTodoData $data): Todo
    {
        return Todo::create([
            'title' => $data->title,
            'user_id' => $data->userId
        ]);
    }
}
