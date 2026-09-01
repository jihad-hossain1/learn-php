<?php

$name = 'alice';

$full_name = '    john doe  ';

echo $full_name;

echo "\n";

$trns = strtoupper(trim($full_name));


echo $trns;

echo "\n";


$email = '  JOHN@EXAMPLE.COM  ';
// $email = null;

$email = strtolower(trim($email));

$company_email = str_contains($email, '@example.com');

$domain = substr($email, strpos($email, '@') + 1);

echo "Email: $email \nDomain: $domain \nCompany email: " . ($company_email ? 'yes' : 'no') . PHP_EOL;

echo "------------ \n";

$string_value = 'str,str2,str3';

$str_arr = explode(',', $string_value);

echo count($str_arr);

echo "----------------------\n";

$arr_to_string = implode(', ', $str_arr);

echo $arr_to_string;


echo "---------------------- \n ";

# Heredoc

$message = <<<TEXT
hello world {$name}

welcome to our app.

thank you.
TEXT;

echo $message;


echo "---------------------- \n ";

$msg = <<<'TEXT'

hello $name

welcome to our app.

thank you.

TEXT;


echo $msg;
