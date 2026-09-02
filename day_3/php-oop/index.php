<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\ProductService;

$productService = new ProductService();

$productService->store('Laptop', 1000.00);
$productService->store('Mouse', 25.00);

$allProducts = $productService->getAllProducts();

foreach ($allProducts as $index => $product) {
    echo "[{$index}] Product: {$product->name}, Price: {$product->price}" . PHP_EOL;
}
