<?php

// Pattern #3 `Factory` 

interface PaymentGateway
{
    public function charge(float $amount): bool;
}

final class StripePayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        echo "charging {$amount} through `stripe`";

        return true;
    }
}


final class PayPalPayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        echo "charing {$amount} through PayPal";

        return true;
    }
}


final class PaymentGatewayFactory
{
    public function create(string $method): PaymentGateway
    {
        return match ($method) {
            'stripe' => new StripePayment(),
            'paypal' => new PayPalPayment(),
            default => throw new InvalidArgumentException(
                "Unsupported payment method: {$method}"
            )
        };
    }
}

$factory = new PaymentGatewayFactory();

$gateway = $factory->create('stripe');

$gateway->charge(100);

echo "" . PHP_EOL;

// Pattern #3 `Strategy`

interface ShippingStrategy
{
    public function calculate(float $weight): float;
}

final class StandardShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 5;
    }
}

final class ExpressShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 10;
    }
}

final class InternationalShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 20;
    }
}


final class ShippingCalculator
{
    public function __construct(
        private ShippingStrategy $strategy
    ) {}

    public function calculate(float $weight): float
    {
        return $this->strategy->calculate($weight);
    }
}


$calculator = new ShippingCalculator(
    new ExpressShipping()
);


$cost = $calculator->calculate(5);

echo $cost . PHP_EOL;


// Pattern #3 — Adapter 

interface SmsSender
{
    public function send(string $phone, string $message): void;
}


final class ExternalSmsProvider
{
    public function sendMessage(string $phone, string $message): void
    {
        echo "SMS Sent." . PHP_EOL;
    }
}

final class SmsProviderAdapter implements SmsSender
{
    public function __construct(
        private ExternalSmsProvider $provider
    ) {}

    public function send(
        string $phone,
        string $message
    ): void {
        $this->provider->sendMessage($phone, $message);
    }
}




echo "" . PHP_EOL;
