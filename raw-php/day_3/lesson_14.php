<?php 


interface PaymentGetway 
{
    public function charge(float $amount): bool ;
}

class CheckoutService 
{
    public function __construct(
        private PaymentGetway $getway
    ){}

    public function checkout(float $amount): bool 
    {
        return $this->getway->charge($amount);
    }

}

class StripePaymentGetway implements PaymentGetway 
{
    public function charge(float $amount): bool 
    {
        echo "Stripe Payment Done: {$amount}." . PHP_EOL;
        return true;
    }

    
}

$checkout = new CheckoutService(
    new StripePaymentGetway()
);

$checkout->checkout(1000);



