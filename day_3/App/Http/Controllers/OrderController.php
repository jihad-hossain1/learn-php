<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;

class OrderController
{
    public function index()
    {
         echo'orders.index';
    }

    public function store(): void 
    {
        $paymentService = new PaymentService();
        $paymentService->pay();
    }
}



