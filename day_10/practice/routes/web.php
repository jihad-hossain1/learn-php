<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/products', function (Request $request) {
    $search = trim($request->query('search', ''));

    printf($search);

    $products = [
        ['slug' => 'iphone-15', 'name' => 'iPhone 15', 'price' => 999, 'description' => 'Apple smartphone.'],
        ['slug' => 'galaxy-s24', 'name' => 'Galaxy S24', 'price' => 899, 'description' => 'Samsung smartphone.'],
        ['slug' => 'nokia-e5', 'name' => 'Nokia E5', 'price' => 399, 'description' => 'Nokia Symbion button smartphone'],
        ['slug' => 'nokia-c5', 'name' => 'Nokia C5', 'price' => 299, 'description' => 'Nokia Symbion button smartphone'],
        ['slug' => 'nokia-n95', 'name' => 'Nokia N95', 'price' => 699, 'description' => 'Nokia Symbion button smartphone'],
        ['slug' => 'nokia-e71', 'name' => 'Nokia E71', 'price' => 599, 'description' => 'Nokia Symbion button smartphone'],
        ['slug' => 'nokia-6600', 'name' => 'Nokia 6600', 'price' => 399, 'description' => 'Nokia Symbion button smartphone'],
    ];
    return view('products.index', [
        'products' => $products,
        'search' => $search
    ]);
});
Route::get('/products/{slug}', function (string $slug) {
    // Temporary sample data until there is a Product model + database table
    $products = [
        ['slug' => 'iphone-15', 'name' => 'iPhone 15', 'price' => 999, 'description' => 'Apple smartphone.'],
        ['slug' => 'galaxy-s24', 'name' => 'Galaxy S24', 'price' => 899, 'description' => 'Samsung smartphone.'],
        ['slug' => 'nokia-e5', 'name' => 'Nokia E5', 'price' => 399, 'description' => 'Nokia Symbion button smartphone'],
    ];

    $product = collect($products)->firstWhere('slug', $slug);

    abort_unless($product !== null, 404);

    return view('products.view', [
        'slug' => $slug,
        'product' => $product,
    ]);
});
