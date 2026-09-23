<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/todos', function () {
    return view('todos.index', ['todos' => [
        [
            "id" => 1,
            'name' => 'hocker'
        ],
        [
            'id' => 2,
            'name' => 'hacker'
        ]
    ]]);
});
Route::get('/todos/show', function(){
    return view('todos.show.index');
});

Route::get('/todos/create',function(){
    return view('todos.create');
});

Route::post('/todos/create',function($request){
    return 'todo created';
})->name('todos.store');