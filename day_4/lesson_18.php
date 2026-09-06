<?php




function numbers(): Generator
{
    echo "Start\n";

    yield 1;

    echo "Middle\n";

    yield 2;

    echo "End\n";

    yield 3;
}

foreach (numbers() as $number) {
    echo "Number: {$number}\n";
}

echo "======================\n";



function getProducts(): Generator
{
    $categories = ['Electronics', 'Clothing', 'Books', 'Food'];

    for ($i = 1; $i <= 100000; $i++) {
        yield [
            'id' => $i,
            'name' => "Product {$i}",
            'category' => $categories[array_rand($categories)],
            'price' => rand(10, 1000) / 10
        ];
    }
}

// Usage
foreach (getProducts() as $product) {
    echo "{$product['name']} - {$product['category']} - \${$product['price']}" . PHP_EOL;
}
