<?php

for ($i = 1; $i <= 5; $i++) {
    echo 'Hello' . PHP_EOL;
}

for ($i = 1; $i <= 10; $i++) {
    echo $i . PHP_EOL;
}


for ($i = 10; $i >= 1; $i--) {
    echo $i . PHP_EOL;
}

echo "\n";

$fruits = array('apple', 'banana', 'orange');

foreach ($fruits as $fruit) {
    echo $fruit . PHP_EOL;
}


echo "\n";

$user = [
    'name' => 'alice',
    'age' => 20,
    'city' => 'USA'
];

foreach ($user as $key => $value) {
    echo "$key: $value" . PHP_EOL;
}

echo "\n";

// nested loop 

for ($row = 1; $row <= 3; $row++) {
    for ($column = 1; $column <= 3; $column++) {
        echo "($row,$column) ";
    }

    echo PHP_EOL;
}

$students = [
    "alice" => 85,
    "bob" => 58,
    "charlie" => 91
];

echo "\n";

foreach ($students as $key => $value) {
    if ($value >= 60) {
        echo "$key - $value - Passed" . PHP_EOL;
    } else {
        echo "$key - $value - Failed" . PHP_EOL;
    }
}

echo "\n";

for ($i = 1; $i <= 10; $i++) {
    echo "5 x $i = " . $i * 5 . PHP_EOL;
}
