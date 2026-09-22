<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string'
        ];
    }
}
