<?php


$prices = [10,20,30];

$discountedPrices = array_map(
    fn(int $price): float => $price * 0.9,
    $prices
);

print_r($discountedPrices);
