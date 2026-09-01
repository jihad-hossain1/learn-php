# 🚀 Module 2 — Lesson 13: Interfaces & Dependency Injection

This is a **very important lesson** for becoming a professional PHP/Laravel developer.

So far you've learned:

```text
Class
  ↓
Object
  ↓
Inheritance
  ↓
Abstract Class
  ↓
Composition
```

Now we're adding:

```text
Interface
   ↓
Dependency Injection
   ↓
Loose Coupling
   ↓
SOLID
   ↓
Laravel Service Container
```

---

# 1. What Is an Interface?

An interface defines **what a class must be able to do**, without defining how it does it.

Think of an interface as a contract.

For example:

```text
PaymentMethod
     │
     │ Contract
     ▼
  "Every payment method
   must have pay()"
```

Different classes can implement that contract differently.

---

# 2. Creating an Interface

```php
<?php

declare(strict_types=1);

interface PaymentMethod
{
    public function pay(float $amount): void;
}
```

This says:

> Any class implementing `PaymentMethod` must provide a `pay()` method accepting a `float` and returning `void`.

The interface doesn't implement the payment itself.

---

# 3. Implementing an Interface

Use:

```php
implements
```

Example:

```php
class CreditCardPayment implements PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using credit card." . PHP_EOL;
    }
}
```

Another implementation:

```php
class CashPayment implements PaymentMethod
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount} using cash." . PHP_EOL;
    }
}
```

Now we have:

```text
          PaymentMethod
             interface
                 │
          ┌──────┴──────┐
          ▼             ▼
 CreditCardPayment  CashPayment
```

Both satisfy the same contract.

---

# 4. Why Do We Need Interfaces?

Imagine you create a checkout function.

Without an interface:

```php
function checkout(CreditCardPayment $payment): void
{
    $payment->pay(100);
}
```

Now `checkout()` specifically depends on:

```text
CreditCardPayment
```

If tomorrow you want:

```text
PayPal
Cash
Bank Transfer
Stripe
```

you may have to modify your code.

That's tight coupling.

---

# 5. Using the Interface

Instead:

```php
function checkout(PaymentMethod $payment): void
{
    $payment->pay(100);
}
```

Now:

```php
checkout(new CreditCardPayment());
checkout(new CashPayment());
```

Both work.

Why?

Because both implement:

```php
PaymentMethod
```

This is **polymorphism**.

---

# 6. The Important Idea

Our `checkout()` function doesn't care **which concrete payment class** it receives.

It only cares:

> "Can this object behave like a `PaymentMethod`?"

That's powerful.

```text
                    checkout()
                        │
                        ▼
                 PaymentMethod
                        │
            ┌───────────┼───────────┐
            ▼           ▼           ▼
          Card        Cash        PayPal
```

The checkout code remains unchanged.

---

# 7. Interface vs Abstract Class

This is a common interview question.

### Abstract class

An abstract class can contain:

* Properties
* Concrete methods
* Abstract methods
* Shared implementation

Example:

```php
abstract class PaymentMethod
{
    protected string $currency = 'USD';

    public function formatAmount(float $amount): string
    {
        return "{$this->currency} {$amount}";
    }

    abstract public function pay(float $amount): void;
}
```

---

### Interface

An interface primarily defines a contract.

```php
interface PaymentMethod
{
    public function pay(float $amount): void;
}
```

A useful mental model:

```text
Abstract class
    ↓
"What you are + shared behavior"

Interface
    ↓
"What you must be able to do"
```

---

# 8. A Class Can Implement Multiple Interfaces

This is one important difference.

A class can implement multiple interfaces:

```php
interface Payable
{
    public function pay(float $amount): void;
}

interface Refundable
{
    public function refund(float $amount): void;
}

class CreditCardPayment implements Payable, Refundable
{
    public function pay(float $amount): void
    {
        echo "Paid {$amount}" . PHP_EOL;
    }

    public function refund(float $amount): void
    {
        echo "Refunded {$amount}" . PHP_EOL;
    }
}
```

So:

```text
             CreditCardPayment
                 /        \
                /          \
           Payable       Refundable
```

This is extremely useful in larger systems.

---

# 9. Dependency Injection

Now we reach a **very important Laravel concept**.

Suppose:

```php
class OrderService
{
    public function __construct(
        private PaymentMethod $paymentMethod,
    ) {
    }

    public function checkout(float $amount): void
    {
        $this->paymentMethod->pay($amount);
    }
}
```

We inject the dependency through the constructor:

```php
$payment = new CreditCardPayment();

$orderService = new OrderService($payment);
```

Then:

```php
$orderService->checkout(500);
```

Output:

```text
Paid 500 using credit card.
```

This is **constructor dependency injection**.

---

# 10. Why Is This Called Dependency Injection?

`OrderService` depends on:

```php
PaymentMethod
```

Instead of creating the dependency internally:

```php
class OrderService
{
    public function checkout(float $amount): void
    {
        $payment = new CreditCardPayment();

        $payment->pay($amount);
    }
}
```

we inject it:

```php
class OrderService
{
    public function __construct(
        private PaymentMethod $paymentMethod,
    ) {
    }
}
```

Conceptually:

```text
Bad:

OrderService
    │
    └── creates CreditCardPayment itself


Good:

CreditCardPayment
       │
       ▼
OrderService
       │
       ▼
uses PaymentMethod
```

---

# 11. Why Is the Second Approach Better?

Because `OrderService` doesn't need to know the concrete implementation.

Today:

```php
new CreditCardPayment()
```

Tomorrow:

```php
new PayPalPayment()
```

Later:

```php
new StripePayment()
```

The `OrderService` doesn't need to change.

---

# 12. Real-World Example

Imagine an application with:

```text
PaymentMethod
      │
 ┌────┼─────────────┐
 ▼    ▼             ▼
Card PayPal       Stripe
```

Our service:

```php
class OrderService
{
    public function __construct(
        private PaymentMethod $paymentMethod,
    ) {
    }

    public function placeOrder(float $amount): void
    {
        $this->paymentMethod->pay($amount);
    }
}
```

We can use:

```php
$orderService = new OrderService(
    new StripePayment(),
);
```

Or:

```php
$orderService = new OrderService(
    new PayPalPayment(),
);
```

The service doesn't care.

---

# 13. Laravel Uses This Everywhere

This concept is fundamental to Laravel.

You'll commonly see:

```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }
}
```

The controller depends on:

```text
OrderService
```

Laravel's **service container** can resolve that dependency automatically.

Eventually you'll write:

```php
public function __construct(
    private PaymentGateway $paymentGateway,
) {
}
```

and Laravel can determine which implementation should be provided when the interface is bound correctly.

---

# 14. Laravel Contract Example

Laravel itself provides many contracts/interfaces.

For example, conceptually:

```php
interface PaymentGateway
{
    public function charge(float $amount): void;
}
```

You might have:

```php
class StripePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): void
    {
        // Stripe implementation
    }
}
```

And:

```php
class PayPalPaymentGateway implements PaymentGateway
{
    public function charge(float $amount): void
    {
        // PayPal implementation
    }
}
```

Your business logic depends on:

```php
PaymentGateway
```

rather than:

```php
StripePaymentGateway
```

This is **loose coupling**.

---

# 15. Loose Coupling

Compare these two designs.

### Tight coupling

```php
class OrderService
{
    public function __construct(
        private StripePaymentGateway $gateway,
    ) {
    }
}
```

`OrderService` is tied directly to Stripe.

### Loose coupling

```php
class OrderService
{
    public function __construct(
        private PaymentGateway $gateway,
    ) {
    }
}
```

Now:

```text
OrderService
      │
      ▼
PaymentGateway
      │
 ┌────┴────┐
 ▼         ▼
Stripe    PayPal
```

That's much more flexible.

---

# 16. Testing Benefit

This is one of the biggest reasons professionals use dependency injection.

Suppose:

```php
class OrderService
{
    public function __construct(
        private PaymentGateway $gateway,
    ) {
    }

    public function checkout(float $amount): void
    {
        $this->gateway->charge($amount);
    }
}
```

During testing, we don't necessarily want to call a real payment provider.

We can create a fake implementation:

```php
class FakePaymentGateway implements PaymentGateway
{
    public float $chargedAmount = 0;

    public function charge(float $amount): void
    {
        $this->chargedAmount = $amount;
    }
}
```

Then:

```php
$gateway = new FakePaymentGateway();

$service = new OrderService($gateway);

$service->checkout(500);

echo $gateway->chargedAmount;
```

Result:

```text
500
```

No real payment was made.

This is one of the reasons dependency injection is so valuable.

---

# 17. Refactor Our Todo Project

Let's apply this to our Todo project.

Imagine we want different storage systems:

```text
TodoRepository
      │
 ┌────┼─────────────┐
 ▼    ▼             ▼
Memory JSON       Database
```

First define the contract:

```php
interface TodoRepository
{
    public function add(array $task): void;

    public function all(): array;

    public function delete(int $taskId): bool;
}
```

Now implement an in-memory repository:

```php
class InMemoryTodoRepository implements TodoRepository
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

    public function delete(int $taskId): bool
    {
        foreach ($this->tasks as $index => $task) {
            if ($task['id'] === $taskId) {
                unset($this->tasks[$index]);

                $this->tasks = array_values($this->tasks);

                return true;
            }
        }

        return false;
    }
}
```

Then our service depends on the interface:

```php
class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {
    }

    public function addTask(string $title): void
    {
        $this->repository->add([
            'id' => random_int(1, 999999),
            'title' => $title,
            'completed' => false,
        ]);
    }

    public function getTasks(): array
    {
        return $this->repository->all();
    }
}
```

Notice the architecture:

```text
CLI / Controller
       │
       ▼
 TodoService
       │
       ▼
TodoRepository
  interface
       │
   ┌───┴────┐
   ▼        ▼
Memory    Database
```

This is getting much closer to professional backend architecture.

---

# 18. Why This Matters in Laravel

Eventually, you might have:

```text
HTTP Request
     ↓
Controller
     ↓
Service
     ↓
Repository / Model
     ↓
Database
```

And dependencies can be injected at each level:

```php
class OrderController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }
}
```

Then:

```php
class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway,
    ) {
    }
}
```

Then Laravel's service container manages the dependency graph.

This is a major reason Laravel applications can remain organized as they grow.

---

# 19. Behind the Scenes: Dependency Graph

Suppose Laravel needs:

```php
OrderController
```

Laravel sees:

```text
OrderController
       │
       ▼
OrderService
       │
       ▼
PaymentGateway
```

If you've registered:

```text
PaymentGateway
       ↓
StripePaymentGateway
```

Laravel can construct:

```text
StripePaymentGateway
       ↓
OrderService
       ↓
OrderController
```

You don't manually instantiate everything.

This is what the **service container** helps solve.

We'll study it deeply later.

---

# 20. Common Mistakes

### ❌ Depending on concrete implementations unnecessarily

Instead of:

```php
private StripePaymentGateway $gateway
```

consider:

```php
private PaymentGateway $gateway
```

when your business logic only needs the contract.

---

### ❌ Creating dependencies inside methods

Avoid:

```php
public function checkout(): void
{
    $gateway = new StripePaymentGateway();

    $gateway->charge(100);
}
```

Prefer dependency injection.

---

### ❌ Creating interfaces for everything

Don't create an interface simply because you can.

This:

```text
UserInterface
UserServiceInterface
UserRepositoryInterface
UserHelperInterface
UserFormatterInterface
...
```

can create unnecessary complexity.

Use interfaces when you actually benefit from:

* Multiple implementations
* Loose coupling
* Testing
* Clear contracts
* Architectural boundaries

---

# ⚡ Performance

Dependency injection and interfaces have a small runtime cost, but it is normally insignificant compared with things like:

* Database queries
* Network requests
* API calls
* File I/O

Don't sacrifice good architecture for microscopic performance gains.

---

# 🔐 Security

Interfaces don't make code automatically secure.

However, separating responsibilities makes it easier to enforce security boundaries.

For example:

```text
Controller
    ↓
Authorization
    ↓
Service
    ↓
Repository
```

A clean architecture makes it easier to ensure authorization isn't accidentally mixed with database or payment code.

---

# 🎯 Practice Exercise

Create:

```php
interface NotificationSender
{
    public function send(string $message): void;
}
```

Then implement:

```text
EmailNotificationSender
SmsNotificationSender
```

Create:

```php
class NotificationService
{
    public function __construct(
        private NotificationSender $sender,
    ) {
    }

    public function notify(string $message): void
    {
        $this->sender->send($message);
    }
}
```

Test it with both implementations.

The key requirement:

> `NotificationService` must not know whether it's using email or SMS.

---

# 🔥 Challenge

Build this architecture:

```text
              PaymentGateway
                   │
          ┌────────┼────────┐
          ▼        ▼        ▼
       Stripe    PayPal    Cash
```

Create:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then:

```php
class CheckoutService
{
    public function __construct(
        private PaymentGateway $paymentGateway,
    ) {
    }

    public function checkout(float $amount): bool
    {
        return $this->paymentGateway->charge($amount);
    }
}
```

Test:

```php
$checkout = new CheckoutService(
    new StripePaymentGateway()
);

$checkout->checkout(1000);
```

Then replace Stripe with PayPal **without changing `CheckoutService`**.

That's the real test of whether you've understood dependency injection.

---

# 🧠 Interview Questions

Try answering these without looking back:

1. What is an interface?
2. What does `implements` mean?
3. Interface vs abstract class?
4. Can a class implement multiple interfaces?
5. What is polymorphism?
6. What is dependency injection?
7. Why use constructor injection?
8. What is loose coupling?
9. Why is depending on an interface useful for testing?
10. What is the difference between composition and inheritance?
11. Why shouldn't you create interfaces for every class?
12. How does dependency injection relate to Laravel's service container?

---

# 📌 The Mental Model

Remember this architecture:

```text
              INTERFACE
             "CONTRACT"
                  │
       ┌──────────┼──────────┐
       ▼          ▼          ▼
  Implementation Implementation Implementation
       │          │          │
       └──────────┼──────────┘
                  ▼
             SERVICE
                  │
          Dependency Injection
                  │
                  ▼
              CONTROLLER
```

And the most important principle:

> **Depend on abstractions when it provides a meaningful boundary, rather than unnecessarily depending on concrete implementations.**

---

# 🔥 Next Lesson — OOP Part 4: SOLID Principles

Next we'll start learning **SOLID**, which separates someone who merely knows PHP syntax from someone who can design maintainable PHP applications.

We'll cover:

```text
S → Single Responsibility Principle
O → Open/Closed Principle
L → Liskov Substitution Principle
I → Interface Segregation Principle
D → Dependency Inversion Principle
```

And we'll apply each principle to our **Todo application and Laravel-style services**.
