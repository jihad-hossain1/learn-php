<?php

declare(strict_types=1);

echo 10 % 3;

$number = 9;
if ($number % 2 === 0) {
    echo 'Even';
}

echo "\n";

$count = 10;

$count += 5;

echo $count;
echo "\n";

$number = 18;

$age = 8;
$is_adult = $age >= 10;

var_dump($is_adult);

echo "\n";

$is_verified = true;

if ($age >= 18 && $is_verified) {
    echo "Access Granted\n";
}

echo "Access not granted\n";

$is_admin = false;
$is_editor = true;

if ($is_admin || $is_editor) {
    echo "can Edit\n";
}

echo "can't edit\n";

$number_addition_string = 5 + "5";

echo $number_addition_string, "\n";

var_dump(5 === '5');

echo "\n";

$name =  $_GET['name'] ?? 'Guest';

$user = null;
echo $user?->name;
