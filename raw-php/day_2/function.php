<?php

function greeting(string $name): string
{
    return "hello $name" . PHP_EOL;
}


echo greeting('karim');
echo greeting('rahim');
echo greeting('rofiq');


function sayNothing()
{
    return 'say hello' . PHP_EOL;
}


function add(int $a, int $b): void
{
    echo $a + $b;
}


add(2, 3);

echo "\n";

// anonymous function 

$anno = function (string $name, bool $is_adult, float $weight): string {
    return "My name {$name}, i adult {$is_adult}, my weight {$weight}";
};


echo $anno(name: 'abc', weight: 33.5, is_adult: true,);

echo "end of line \n";

function like(int $a, int $b, float $c): float
{
    return $a + $b + $c;
}

echo like(b: 1, c: 0.3, a: 1);
echo PHP_EOL;


$numbers = [1, 2, 3, 4];

$squares = array_map(function (int $num): int {
    return $num * $num;
}, $numbers);


$another_short_arrow_fn = array_map(
    fn(int $num): int => $num * $num,
    $numbers
);


$result = $another_short_arrow_fn($numbers);

echo $result;
