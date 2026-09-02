<?php

declare(strict_types=1);

function divide(float $a, float $b): float
{
    if($b === 0.0){
        throw new InvalidArgumentException("can't divide by zero.");
    }

    return $a / $b;
}

try{
    echo divide(10,0);
} catch(InvalidArgumentException $exception){
    echo $exception->getMessage();
}