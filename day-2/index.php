<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    // local variables
    $firstName = "John";
    $lastName = "Doe";
    $age = 30;

    echo "$firstName $lastName is $age years old. <br>";

    // Associative array
    $person_2 = [
        'firstName' => "Smith",
        'lastName' => "Doe",
        'age' => 35
    ];

    // Accessing associative array values
    echo $person_2["firstName"] . " " . $person_2["lastName"] . " is " . $person_2["age"] . " years old.";

    echo "<br>";

    // array 
    $fruits_list = ["banana", "apple", "orange", "avocado"];
    // for($fr = 0; $fr <= $fruits_list ; $fr++){
    //     echo "$fr";

    // }

    echo $fruits_list[0] . " " . $fruits_list[1] . " " . $fruits_list[2] . " " . $fruits_list[3];

    echo "<br>";

    // Global variables
    define("NAME", "John Doe");
    define("AGE", 30);
    define("COUNTRY", "USA");
    define("CITY", "New York");
    define("EMAIL", "example@gmail.com");

    echo NAME . " is " . AGE . " years old. <br>";
    echo "He lives in " . CITY . ", " . COUNTRY . ". <br>";


    // function use case and declaration

    echo "<br>";

    function php_function()
    {
        echo NAME;
        $fruit = "Watermelon";

        echo "<br>";
        echo "Eat an $fruit";
    }

    php_function();

    echo "<br>";


    function test_data_params($data)
    {
        echo "User Name : " . $data["firstName"] . " " . $data["middleName"] . " " . $data["lastName"];
    }


    test_data_params([
        "firstName" => "ali",
        "middleName" => "asraf",
        "lastName" => "khan",
        "age" => 30
    ]);

    echo "<br>";

    // check variable are which type
    var_dump($firstName);



    // create an object

    class Car
    {
        var $model;
        function carModel($number)
        {
            global $model;
            $model = $number;
            echo "This is $model <br>";
        }
    }

    echo "<br>";

    $toyota = new Car;
    $toyota->carModel("Axio 2025 model");

    $mazda = new Car;
    $mazda->carModel("Mazda 2025 model");

    echo "<br>";

    $string_length_count = "I love watermelon";

    // count string length
    echo strlen("string length = $string_length_count");
    echo "<br>";

    // reverse text
    echo strrev($string_length_count);
    echo "<br>";

    // find position on an word to string
    echo strpos($string_length_count, "love");
    echo "<br>";

    // word replace in string
    echo str_replace("watermelon", "banana", $string_length_count);
    echo "<br>";

    //basic syntax
    $x = 5; // Int
    $y = 55.55; // float
    $z = true; // boolean;
    $string_of_list = ["value one", "value 2"];
    $object_of_list = [$toyota, $mazda];

    var_dump(
        $x,
        $y,
        $z,
        $string_of_list,
        $object_of_list
    );
    echo "<br>";

    // Math operation
    $list_of_numbers = [23, 43, 53, 533];
    // find max min number
    echo (max($list_of_numbers));
    echo "<br>";
    echo (min($list_of_numbers));



    echo "<br>";

    // if else statement
    if (5 >= 5) {
        echo "hello";
    } elseif (5 > 8) {
        echo "hello 2";
    } else {
        echo "world";
    } ;

    echo "<br>";

    // loop
    $nn = 3;
    do{
        echo "The number is: $nn <br>";
        $nn++;
    } while($nn <=5 );
    echo "<br>";

    for($y = 1; $y <= 20; $y+=1){
        echo "the number is: $y <br>";
    };

    echo "<br>";

    // date
    // fix time zone 
    date_default_timezone_set("Asia/Dhaka");

    $today = "Today is " . date("y-m-d");
    $month = date("m");
    $year = date("y");
    $day = date("d");
    $current_time  = date("h:i:sa");

    echo "current_time" . $current_time;

    
    echo "<br>";


    ?>
</body>

</html>