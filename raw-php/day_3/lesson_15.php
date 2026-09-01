<?php

declare(strict_types=1);



use \App\Services\CheckoutService;


$paymentService = new CheckoutService(
    new \App\Services\StripePaymentGetway()
);
