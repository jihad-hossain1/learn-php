<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CodeController;


Route::get('/', fn() => view('welcome'));

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get("/products/new", [ProductController::class, 'newform']);
Route::get('/products/update/{slug}', [ProductController::class, 'update']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

Route::get('/lookups', [CodeController::class, 'index']);
Route::post('/lookups', [CodeController::class, 'store']);
