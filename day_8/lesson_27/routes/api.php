<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesCreditNoteController;
use App\Http\Controllers\AuthController;

// Basic Route define

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/todos', function () {
    return 'Todo list';
});

Route::post("/todos", function () {
    return "todo created";
});

// single parameter route: /todos/123
Route::get('/todos/{id}', function (int $id) {
    return "get todo single id: {$id}";
})->whereNumber('id');

Route::put('/todos/{id}', function (int $id) {
    return "todo updated id: {$id}";
});

Route::patch('/todos/{todo}', function () {
    return "Todo patch done";
});

Route::delete('/todos/{todo}', function () {
    return 'todo remove done.';
});

// multiple parameter route : /todos/123/users/123
Route::get("/todos/{todo}/users/{user}", function (int $todo, int $user) {
    return "Todo ID: {$todo} - User ID: {$user}";
});

// Group Routes 
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        return "Admin users list";
    });

    Route::get('/orders', function () {
        return "Admin order list.";
    });
});

// Optional parameters
Route::get('/todos/{name?}', function (?string $name = null) {
    return $name ?? 'Guest';
})->whereAlpha('name');

Route::get('/todos/{slug}', function (string $slug) {
    return "you got an slug todo: {$slug}";
})->where('slug', '[a-z0-9]+');


// Named Routes Define
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

Route::name('super-admin.')->group(function () {
    Route::get('/users', [ClientController::class, 'superAdminUsers'])->name('users');

    Route::get('/orders', [ClientController::class, 'superAdminOrder'])->name('orders');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::prefix('custom')->name('custom.')->group(function () {

    Route::get('/products', [ProductController::class, 'customProducts'])->name('products.custom.customProducts');
});


Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/reports', [SalesController::class, 'reports'])->name('sales.reports');
    Route::post('/credit-note', [SalesCreditNoteController::class, 'store'])->name('sales.credit.note.store');
    Route::get('/credit-note', [SalesCreditNoteController::class, 'index'])->name('sales.credit.note.index');
    Route::get('/{id}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('/reports/{id}/view', [SalesController::class, 'reportsView'])->name('sales.reports.view');
});

Route::get('/login', function () {
    return "login to dash.";
})->name('login');


// Middleware Route define
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json([
            'message' => 'Dashboard info here',
        ]);
    });
});

Route::middleware('logging')->group(function () {
    Route::get('/customers', function () {
        return 'customers info here';
    });
});
