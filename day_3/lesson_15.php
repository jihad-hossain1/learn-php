<?php

declare(strict_types=1);



use \App\Services\PaymentService;


$paymentService = new PaymentService();

$paymentService->pay();