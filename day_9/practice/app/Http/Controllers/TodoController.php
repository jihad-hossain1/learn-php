<?php 


namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Todo;

class TodoController extends Controller 
{
    public function index(): View
    {
        return view('todos.index');
    }

    public function show(Todo $todo): View
    {
        return view('todos.show',[
            'todo'=> $todo
        ]);
    }
}


