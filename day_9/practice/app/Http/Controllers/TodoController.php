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

    public function show(): View
    {
        return view('todos.show',[
            'todo'=> [
                'id' => 1,
                'title' => 'abc efx'
            ]
        ]);
    }
}


