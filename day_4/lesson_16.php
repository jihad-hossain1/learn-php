<?php


declare(strict_types=1);

class InsufficientStockException extends RuntimeException
{
}

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
    echo $exception->getMessage() . PHP_EOL;
}

try{
 echo 'Processing....' . PHP_EOL;

 throw new RuntimeException('Failed.');
} catch(RuntimeException $exception){

 echo $exception->getMessage() . PHP_EOL;
} finally {

    echo 'Finished.' . PHP_EOL;
}

class Product 
{
    public function __construct( 
        public readonly float $price,
        public int $stock
    ){}
   
}

class CheckoutService
{
    public function __construct()
    {
    }

    public function purchase(
        Product $product,
        int $quantity,
    ): float {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        if ($quantity > $product->stock) {
            throw new InsufficientStockException(
                'Not enough stock.'
            );
        }

        $product->stock -= $quantity;

        return $product->price * $quantity;
    }
}


$product = new Product(
    price: 100,
    stock: 5
); 

$checkout = new CheckoutService();

try {
    $total = $checkout->purchase($product,10);

    echo "Total: {$total}";
} catch (InsufficientStockException $exception) {

    echo $exception->getMessage();
}

class InsufficientBalanceException extends Exception 
{
 // 
}


class BankAccount 
{
    public function __construct(
        private float $balance
    ){}

    public function withdraw(float $amount): void 
    {
        if($amount <= 0){
            throw new InvalidArgumentException(
                'Amount must be greater then 0'
            );
        }

        if($amount > $this->balance){
            throw new InsufficientBalanceException(
            'Insufficient Balance.'
            );
        }

        echo "withdraw successful. $$amount" . PHP_EOL ; 
    }

    public function balance(): float 
    {
        return $this->balance;
    }
}

try {
    $account = new BankAccount(1000);

    $current_balance = $account->balance();

    echo "Balance: {$current_balance}" . PHP_EOL;

    $account->withdraw(2000);

    echo "Balance: {$current_balance}" . PHP_EOL;

} catch (InsufficientBalanceException $exception) {
    echo $exception->getMessage();
}







