<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/products', function () {
    $products = [
        'iphone-15' => ['name' => 'iPhone 15', 'price' => 999, 'description' => 'Apple smartphone.'],
        'galaxy-s24' => ['name' => 'Galaxy S24', 'price' => 899, 'description' => 'Samsung smartphone.'],
    ];
    return view('products.index', ['products' => $products]);
});
Route::get('/products/{slug}', function (string $slug) {
    // Temporary sample data until there is a Product model + database table
    $products = [
        'iphone-15' => ['name' => 'iPhone 15', 'price' => 999, 'description' => 'Apple smartphone.'],
        'galaxy-s24' => ['name' => 'Galaxy S24', 'price' => 899, 'description' => 'Samsung smartphone.'],
    ];

    abort_unless(isset($products[$slug]), 404);

    return view('products.view', [
        'slug' => $slug,
        'product' => $products[$slug],
    ]);
});
