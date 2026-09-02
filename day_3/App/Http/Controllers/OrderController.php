<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\OrderService;

class OrderController
{

    public function __construct(
        private OrderService $orderService
    ){}

    public function index()
    {
         echo'orders.index';
    }

    public function store(): void 
    {
        $order = $this->orderService->create();
    
        $paymentService = new PaymentService();
        $paymentService->pay();
    }
}



