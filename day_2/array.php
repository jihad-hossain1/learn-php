<?php

$numbers = [50, 10, 30, 20];

// sorting method
sort($numbers);

// reverse sorting
// rsort($numbers);


// print_r($numbers);


echo "\n";

// spread operator 

$list_one = ['apple', 'banana'];
$list_two = ['guava', 'pineapple'];


$fruits = [...$list_one, ...$list_two];

// print_r($fruits);

echo "\n";


$products = [
    ['name' => 'Laptop', 'price' => 800],
    ['name' => 'Mouse', 'price' => 20],
    ['name' => 'Keyboard', 'price' => 50],
];

foreach ($products as $product) {
    echo "{$product['name']} - $" . "{$product['price']}" . PHP_EOL;
}

echo "\n";

$total_price = array_reduce(
    $products,
    fn($carry, $item) => $carry + $item['price'],
    0
);

echo "\n";

echo $total_price;

echo "\n";

$prices = [100, 200, 300, 400];

$modify_prices_with_tax = array_map(
    fn(int $num): int => $num + 10,
    $prices
);


echo "\n";

$fruits = ['a', 'b'];

$fruits[] = 'c';

print_r($fruits);

echo "\n";

$fruits[1] = 'd';

echo "\n";

unset($fruits[1]);

print_r($fruits);

echo "\n";

$fruits = array_values($fruits);

print_r($fruits);

echo "\n";

$numbers = [1, 3, 2, 6, 5];
asort($numbers);

print_r($numbers);

function calculate(array $products): float
{
    $total = 0;

    foreach ($products as $product) {
        $price = (int) $product['price'];
        $quantity = (int) $product['quantity'];

        $total += $price * $quantity;
    }

    return $total;
};

function calculate1(array $products): float
{
    $total = 0;

    foreach ($products as $product) {
        $price = (int) $product['price'];
        $quantity = (int) ($product['quantity'] ?? 0);

        $total += $price * $quantity;
    }

    return $total;
}


$products = [
    [
        'name' => 'Laptop',
        'price' => 800,
        'quantity' => 2,
    ],
    [
        'name' => 'Mouse',
        'price' => 20,
        'quantity' => 3,
    ],
    [
        'name' => 'Keyboard',
        'price' => 50,
        'quantity' => 1,
    ],
];

echo calculate($products);
