<?php

declare(strict_types=1);

$as_string = "Hello world";
$as_number = 123;
$as_boolean = false;
$as_float = 34.56;
$as_list = ['one', 'tow', 'three'];
$as_list_define_with_custom_index = ['1' => 'one', '2' => 'two', '3' => 'there'];
$as_list_array = array('1', '2', '3');
$is_middle_name = null;

// var_dump($as_string);
// echo $as_number;
// print_r($as_list_define_with_custom_index);
// print_r($as_list);
// print_r($as_list_array);


$concat_string = 'hey' . ' ' . 'you';
// echo $concat_string;

$different_type_string = "say $as_string again say twitch $as_string";
$different_type_string = 'say $as_string again say twitch $as_string';
// echo $different_type_string;
