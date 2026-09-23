<?php


namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index(): View
    {
        return view('todos.index')->with([
            'todo' => [
                'id' => 2,
                'title' => 'ebc efx',
                'priority' => 2
            ]
        ]);
    }

    public function show(): View
    {
        return view('todos.show')->with([
            'todo' => [
                'id' => 1,
                'title' => 'abc efx',
                'priority' => 1
            ]
        ]);
    }
}
