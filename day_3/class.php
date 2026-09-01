<?php

declare(strict_types=1);

class Product
{
    public string $name;
    public float $price = 0;

    public function __construct(string $name, float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function getDetails()
    {
        echo $this->name . " - " . '$' . $this->price . PHP_EOL;
    }
}


$prod = new Product('Laptop', 100000);

$prod->getDetails();


echo "" . PHP_EOL;


class Vehicle
{
    protected string $brand;

    public function __construct(string $brand)
    {
        $this->brand = $brand;
    }

    public function start(): void
    {
        echo "[$this->brand] Vehicle warm up..." . PHP_EOL;
    }
}


class Cars extends Vehicle
{
    private string $model;

    public function __construct(
        string $brand,
        string $model,
    ) {
        parent::__construct($brand);

        $this->model = $model;
    }

    public function specification()
    {
        parent::start();
        echo $this->model . "started." . PHP_EOL;
    }
}



$car = new Cars('Toyota', 'Corolla');
$car->start();


echo "" . PHP_EOL;


abstract class PaymentMethod
{
    abstract public function pay(float $amount): void;
}


class CreditCardPayment extends PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using CreditCard." . PHP_EOL;
    }
}

class PaypalPayment extends PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using Paypal." . PHP_EOL;
    }
}


function processPayment(PaymentMethod $payment): void
{
    $payment->pay(100);
}


processPayment(new CreditCardPayment());
processPayment(new PaypalPayment());


echo "------TODO------" . PHP_EOL;


class TodoList 
{
    private array $tasks = [];

    public function list(): array 
    {
        $this->tasks;
    }
}


class TodoRepository 
{
    private array $tasks = [];

    public function add(array $task): void 
    {
        $this->tasks[] = $task;
    }

    public function all(): array 
    {
        return $this->tasks;
    }

}

class TodoService 
{
    public function __construct(
        private TodoRepository $repository
    ){

    }

    public function addTask(string $title): void 
    {
        $task = [
            'title'=>$title,
            'completed' => false,
        ];

        $this->repository->add($task);
    }

}

$t_list = new TodoList();

$repository = new TodoRepository();

$service = new TodoService($repository);
$service->addTask('Learn Laravel');



print_r($t_list->list());
