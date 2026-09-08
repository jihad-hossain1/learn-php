<?php 

// declare(strict_types=1)

// $users = ['user','sarah','mike'];

// foreach($users as $user){
//     echo $user;
// }


interface Iterator extends Traversable
{
    public function current(): mixed;

    public function key(): mixed;

    public function next(): void;

    public function rewind(): void;

    public function valid(): bool;
}




// class UserCollection 
// {
//     private array $users = [];

//     //

//     // public function
// }

// $users = new UserCollection();

// foreach($users as $user){
//     //
// }


 class ProductIterator implements Iterator
{
    private int $position = 0;

    public function __construct(
        private array $products,
    ) {
    }

    public function current(): mixed
    {
        return $this->products[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->products[$this->position]);
    }
}


$products = new ProductIterator([
    'Laptop',
    'Keyboard',
    'Mouse'
]);


foreach($products as $product){
    echo $product . PHP_EOL;
}

