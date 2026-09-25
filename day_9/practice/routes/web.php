<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/todos');

Route::middleware(['todo.activity'])->prefix('todos')->name('todos.')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('index');
    Route::get('/create', [TodoController::class, 'create'])->name('create');
    Route::post('/', [TodoController::class, 'store'])->name('store');
    Route::get('/{id}', [TodoController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [TodoController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TodoController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle', [TodoController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [TodoController::class, 'destroy'])->name('destroy');
});
