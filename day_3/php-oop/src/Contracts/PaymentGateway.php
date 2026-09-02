<?php 

namespace App\Contracts;

interface IPaymentGateway
{
    public function charge(float $amount): bool;
}


class PaymentGateway implements IPaymentGateway
{
    public function charge(float $amount): bool
    {
        // Simulate charging the amount using Stripe API
        echo "Charging {$amount} using Stripe." . PHP_EOL;
        return true; // Simulate a successful charge
    }
}
