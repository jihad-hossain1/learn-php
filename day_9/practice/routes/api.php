<?php

use App\Http\Controllers\Api\TodoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['todo.activity'])->prefix('todos')->name('api.todos.')->group(function () {
    Route::get('/', [TodoApiController::class, 'index'])->name('index');
    Route::post('/', [TodoApiController::class, 'store'])->name('store');
    Route::get('/{id}', [TodoApiController::class, 'show'])->name('show');
    Route::put('/{id}', [TodoApiController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle', [TodoApiController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [TodoApiController::class, 'destroy'])->name('destroy');
});
