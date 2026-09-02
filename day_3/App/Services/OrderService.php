<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function create(): Order
    {
        echo 'Order created.';
        return new Order();
    }
}