# 🚀 Module 2 — Lesson 12: OOP Part 2 — Inheritance & Composition

Today we'll go one level deeper into OOP.

We'll learn:

- Inheritance
- `extends`
- Parent and child classes
- `protected`
- Method overriding
- `parent::`
- Abstract classes
- Composition
- **Inheritance vs composition**
- How these concepts appear in Laravel

---

# 1. What Is Inheritance?

Inheritance allows one class to reuse or extend another class.

Imagine:

```text
Vehicle
   │
   ├── Car
   └── Motorcycle
```

A car **is a** vehicle.

A motorcycle **is a** vehicle.

So we can put common behavior in `Vehicle` and specialize it in `Car` and `Motorcycle`.

---

# 2. Basic Example

Create a parent class:

```php
<?php

declare(strict_types=1);

class Vehicle
{
    public function start(): void
    {
        echo 'Vehicle started.' . PHP_EOL;
    }
}
```

Now create a child:

```php
class Car extends Vehicle
{
}
```

Because `Car` extends `Vehicle`, it inherits `start()`.

```php
$car = new Car();

$car->start();
```

Output:

```text
Vehicle started.
```

We didn't write `start()` inside `Car`.

It came from `Vehicle`.

---

# 3. `extends`

This:

```php
class Car extends Vehicle
{
}
```

means:

> `Car` inherits from `Vehicle`.

The relationship is:

```text
Vehicle
   ▲
   │ extends
   │
  Car
```

---

# 4. Adding Child-Specific Methods

The child can have its own methods.

```php
class Car extends Vehicle
{
    public function openTrunk(): void
    {
        echo 'Trunk opened.' . PHP_EOL;
    }
}
```

Now:

```php
$car = new Car();

$car->start();
$car->openTrunk();
```

Output:

```text
Vehicle started.
Trunk opened.
```

So the child gets:

```text
Parent behavior
      +
Child behavior
```

---

# 5. Properties and `protected`

Suppose the parent has a property:

```php
class Vehicle
{
    protected string $brand;

    public function __construct(string $brand)
    {
        $this->brand = $brand;
    }
}
```

Why `protected`?

Because the property should be accessible inside:

- `Vehicle`
- Classes extending `Vehicle`

but not directly from outside.

So:

```php
class Car extends Vehicle
{
    public function showBrand(): void
    {
        echo $this->brand . PHP_EOL;
    }
}
```

works.

But:

```php
$car->brand;
```

doesn't work because `$brand` is protected.

---

# 6. `public` vs `protected` vs `private`

This is very important.

| Visibility  | Same class | Child class | Outside |
| ----------- | ---------: | ----------: | ------: |
| `public`    |         ✅ |          ✅ |      ✅ |
| `protected` |         ✅ |          ✅ |      ❌ |
| `private`   |         ✅ |          ❌ |      ❌ |

Think:

```text
public
  ↓
Everyone

protected
  ↓
Class + children

private
  ↓
Only this class
```

---

# 7. Method Overriding

A child class can replace a parent's method.

Parent:

```php
class Vehicle
{
    public function start(): void
    {
        echo 'Vehicle started.' . PHP_EOL;
    }
}
```

Child:

```php
class Car extends Vehicle
{
    public function start(): void
    {
        echo 'Car engine started.' . PHP_EOL;
    }
}
```

Now:

```php
$car = new Car();

$car->start();
```

Output:

```text
Car engine started.
```

The child's method overrides the parent's method.

---

# 8. Calling the Parent Method

Sometimes you want to extend the parent's behavior rather than completely replace it.

Use:

```php
parent::
```

Example:

```php
class Vehicle
{
    public function start(): void
    {
        echo 'Checking vehicle...' . PHP_EOL;
    }
}

class Car extends Vehicle
{
    public function start(): void
    {
        parent::start();

        echo 'Starting car engine...' . PHP_EOL;
    }
}
```

Now:

```php
$car->start();
```

Output:

```text
Checking vehicle...
Starting car engine...
```

Conceptually:

```text
Car::start()
     │
     ├── parent::start()
     │
     └── Car-specific logic
```

---

# 9. Constructor Inheritance

Suppose:

```php
class Vehicle
{
    protected string $brand;

    public function __construct(string $brand)
    {
        $this->brand = $brand;
    }
}
```

A child doesn't need to define its own constructor:

```php
class Car extends Vehicle
{
}
```

You can do:

```php
$car = new Car('Toyota');
```

The inherited constructor is used.

---

# 10. Child Constructor

If the child needs additional data:

```php
class Car extends Vehicle
{
    private string $model;

    public function __construct(
        string $brand,
        string $model,
    ) {
        parent::__construct($brand);

        $this->model = $model;
    }
}
```

Now:

```php
$car = new Car('Toyota', 'Corolla');
```

The child constructor handles the model and passes the brand to the parent.

```text
Car::__construct()
       │
       ├── parent::__construct($brand)
       │
       └── $this->model
```

---

# 11. Abstract Classes

Sometimes you don't want users to create an instance of a parent class.

For example:

```text
PaymentMethod
    │
    ├── CreditCardPayment
    └── PayPalPayment
```

`PaymentMethod` is conceptually a base class.

You can make it abstract:

```php
abstract class PaymentMethod
{
}
```

Now this is invalid:

```php
$payment = new PaymentMethod();
```

You cannot instantiate an abstract class.

But you can extend it:

```php
class CreditCardPayment extends PaymentMethod
{
}
```

---

# 12. Abstract Methods

An abstract class can require child classes to implement certain methods.

```php
abstract class PaymentMethod
{
    abstract public function pay(float $amount): void;
}
```

Now:

```php
class CreditCardPayment extends PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using credit card." . PHP_EOL;
    }
}
```

Another implementation:

```php
class PayPalPayment extends PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using PayPal." . PHP_EOL;
    }
}
```

Now both classes must provide:

```php
pay(float $amount): void
```

---

# 13. Why Abstract Classes Are Useful

They allow you to define a common contract **and** share implementation.

For example:

```text
             PaymentMethod
             abstract class
                  │
          ┌───────┴───────┐
          ▼               ▼
    CreditCard          PayPal
       pay()             pay()
```

Different classes can have different implementations while following the same basic structure.

---

# 14. Polymorphism

This is a major OOP concept.

Because both classes extend `PaymentMethod`, you can type-hint the parent:

```php
function processPayment(PaymentMethod $payment): void
{
    $payment->pay(100);
}
```

Now:

```php
processPayment(new CreditCardPayment());
processPayment(new PayPalPayment());
```

The same function works with different payment implementations.

That's **polymorphism**.

Conceptually:

```text
processPayment()
       │
       ▼
PaymentMethod
       │
   ┌───┴────┐
   ▼        ▼
Card      PayPal
```

This idea becomes extremely important when we reach **interfaces and dependency injection**.

---

# 15. But There's a Problem With Inheritance

Inheritance is powerful, but beginners often overuse it.

Suppose you have:

```text
Animal
  │
  └── Dog
```

That's reasonable.

But imagine:

```text
User
  │
  └── EmailService
```

Does an `EmailService` **is a** User?

No.

The relationship doesn't make sense.

This leads us to one of the most important design principles:

> **Use inheritance for genuine "is-a" relationships.**

---

# 16. Composition

Composition means one object **uses or contains another object**.

Example:

```text
Car
 │
 └── Engine
```

A car **has an** engine.

It isn't an engine.

So instead of:

```php
class Car extends Engine
{
}
```

we use:

```php
class Engine
{
    public function start(): void
    {
        echo 'Engine started.' . PHP_EOL;
    }
}
```

Then:

```php
class Car
{
    public function __construct(
        private Engine $engine,
    ) {
    }

    public function start(): void
    {
        $this->engine->start();

        echo 'Car started.' . PHP_EOL;
    }
}
```

Use it:

```php
$engine = new Engine();

$car = new Car($engine);

$car->start();
```

Output:

```text
Engine started.
Car started.
```

---

# 17. Inheritance vs Composition

This distinction is extremely important for Laravel.

### Inheritance

```php
class Car extends Vehicle
```

Means:

> Car **is a** Vehicle.

### Composition

```php
class Car
{
    public function __construct(
        private Engine $engine,
    ) {
    }
}
```

Means:

> Car **has an** Engine.

A useful rule:

```text
"is-a"  → consider inheritance

"has-a" → consider composition
```

But even for "is-a", inheritance isn't automatically the best choice. Often composition gives you more flexibility.

---

# 18. Real Laravel Example

You've probably seen this:

```php
class UserController extends Controller
{
}
```

That's inheritance.

Laravel's controller system provides common controller behavior through the base `Controller`.

But you'll also frequently see composition:

```php
class OrderController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }
}
```

The controller **has an** `OrderService`.

Then:

```php
public function store(): Response
{
    $this->orderService->create();

    // ...
}
```

This is a very important professional pattern:

```text
Controller
    │
    └── OrderService
            │
            └── Business Logic
```

Rather than putting all business logic inside the controller.

---

# 19. Why Composition Is Powerful

Imagine your `OrderController` depends on:

```php
PaymentService
InventoryService
EmailService
```

You can compose them:

```php
class OrderService
{
    public function __construct(
        private PaymentService $paymentService,
        private InventoryService $inventoryService,
        private EmailService $emailService,
    ) {
    }
}
```

Now each class has a focused responsibility.

This supports:

- Maintainability
- Testing
- Reusability
- Dependency Injection
- SOLID principles

We'll go much deeper into this later.

---

# 20. Refactoring Our Todo App

Our previous Todo app had:

```php
class TodoList
{
    private array $tasks = [];
}
```

We could now make another class responsible for storing tasks:

```php
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
```

Then our Todo service can use it:

```php
class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {
    }

    public function addTask(string $title): void
    {
        $task = [
            'title' => $title,
            'completed' => false,
        ];

        $this->repository->add($task);
    }
}
```

Now:

```text
TodoService
     │
     │ has-a
     ▼
TodoRepository
     │
     ▼
Task data
```

This is **composition**.

And this architecture is much closer to what you'll see in professional Laravel projects.

---

# 21. Behind the Scenes

When you write:

```php
$service = new TodoService($repository);
```

PHP stores the repository object inside the service:

```text
$service
   │
   └── $repository
          │
          └── TodoRepository object
```

Then:

```php
$service->addTask('Learn Laravel');
```

can internally call:

```php
$this->repository->add($task);
```

This is the foundation of **dependency injection**.

Laravel's service container will eventually automate much of this object creation for us.

---

# 22. Common Mistakes

### ❌ Inheriting just to reuse code

Don't do:

```php
class EmailService extends SomeRandomClass
```

just because the parent has a useful method.

Prefer composition or a reusable service.

---

### ❌ Making deep inheritance trees

Avoid architectures like:

```text
A
└── B
    └── C
        └── D
            └── E
```

These can become difficult to understand and modify.

---

### ❌ Making everything `protected`

Don't choose `protected` automatically.

Prefer the narrowest visibility appropriate for the design.

Usually:

```text
private
```

is a good default for internal state.

---

### ❌ Calling parent unnecessarily

Don't write:

```php
parent::method();
```

unless you actually need the parent's behavior.

---

# ⚡ Performance

Inheritance itself usually isn't something you should optimize prematurely.

The bigger concern is architecture.

Poor architecture creates:

- Duplicate work
- Hard-to-test code
- Tight coupling
- Difficult refactoring

Good object design generally matters much more than tiny differences in method-call overhead.

---

# 🔐 Security

OOP doesn't automatically make an application secure.

But encapsulation can reduce accidental access to sensitive state.

For example:

```php
private string $passwordHash;
```

is better than:

```php
public string $passwordHash;
```

Also remember:

```text
private ≠ encrypted
```

It only controls PHP-level access.

Sensitive data still needs appropriate hashing/encryption and access controls.

---

# 🎯 Practice Exercise

Create:

```php
abstract class Notification
```

with:

```php
abstract public function send(string $message): void;
```

Then create:

```text
EmailNotification
SmsNotification
```

Both should extend `Notification`.

Example:

```php
$email = new EmailNotification();

$email->send('Your order has shipped.');
```

Expected:

```text
Sending email: Your order has shipped.
```

---

# 🔥 Challenge Exercise

Create:

```text
PaymentMethod
    │
    ├── CreditCardPayment
    └── CashPayment
```

`PaymentMethod` should be abstract.

Every payment method must implement:

```php
pay(float $amount): void
```

Then create:

```php
function checkout(PaymentMethod $payment): void
{
    $payment->pay(500);
}
```

Test:

```php
checkout(new CreditCardPayment());
checkout(new CashPayment());
```

The important thing is that `checkout()` doesn't need to know the concrete payment type.

That's your first practical experience with **polymorphism**.

---

# 🧠 Interview Questions

Try answering these before looking up the answers:

1. What is inheritance?
2. What does `extends` do?
3. What is method overriding?
4. What does `parent::` do?
5. What's the difference between `private` and `protected`?
6. What is an abstract class?
7. Can you instantiate an abstract class?
8. What is polymorphism?
9. What is composition?
10. What's the difference between **"is-a"** and **"has-a"**?
11. Why is composition often preferred over inheritance?
12. How does composition relate to dependency injection?

---

# 📌 The Big Picture

You should now understand:

```text
                 OOP
                  │
       ┌──────────┼──────────┐
       ▼          ▼          ▼
    Classes     Objects   Encapsulation
       │
       ▼
  Inheritance
       │
       ▼
  Polymorphism
       │
       ▼
  Composition
       │
       ▼
Dependency Injection
```

And this last part is particularly important:

```text
Composition
     ↓
Dependency Injection
     ↓
Service Container
     ↓
Laravel
```

## 🔥 Next Lesson: Interfaces

Next we'll learn one of the **most important concepts for professional Laravel development**:

- Interfaces
- `implements`
- Contracts
- Abstract class vs interface
- Polymorphism with interfaces
- Dependency Injection
- Why Laravel uses contracts
- Practical payment example
- Refactoring our Todo project using an interface
- First look at the **SOLID principles**
