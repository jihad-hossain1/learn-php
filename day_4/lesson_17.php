<?php

declare(strict_types=1);


enum OrderStatus
{
    case Pending;
    case Processing;
    case Completed;
    case Cancelled;
}

$status = OrderStatus::Pending;

if ($status === OrderStatus::Pending) {
    echo "Order is waiting." . PHP_EOL;
}


enum BackendOrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}


$backend_status = BackendOrderStatus::Pending;
// $backend_status = BackendOrderStatus::from('pending');
// $backend_status = BackendOrderStatus::tryFrom('pending');

echo $backend_status->value . PHP_EOL;

foreach (BackendOrderStatus::cases() as $status) {
    echo $status->value . PHP_EOL;
}

enum OdrStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Waiting for payment',
            self::Processing => 'Being prepared',
            self::Completed => 'Order completed',
            self::Cancelled => 'Order cancelled'
        };
    }
}


$odr_status = OdrStatus::Processing;

echo $odr_status->label() . PHP_EOL;

echo "===================" . PHP_EOL;

enum OStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function default(): self
    {
        return self::Pending;
    }
}

$o_status = OStatus::default();

echo '================' . PHP_EOL;


enum UserRole: int
{
    case Customer = 1;
    case Admin = 2;
    case Manager = 3;
}

echo UserRole::Admin->value . PHP_EOL;
