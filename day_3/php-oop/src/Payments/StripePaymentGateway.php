<?php

use App\Contracts\PaymentGateway;

class StripePaymentGateway extends PaymentGateway
{
    public function charge(float $amount): bool
    {
        // Simulate charging the amount using Stripe API
        echo "Charging {$amount} using Stripe." . PHP_EOL;
        return true; // Simulate a successful charge
    }
}



