# Lesson 23 — PHP Design Patterns

Now we're moving from **PHP language features** into **professional application architecture**.

You've already learned:

```text
PHP Fundamentals
      ↓
OOP
      ↓
Interfaces
      ↓
Abstract Classes
      ↓
SOLID
      ↓
Composer / Autoloading
      ↓
Exceptions
      ↓
Enums
      ↓
Generators
      ↓
Attributes
      ↓
Iterators
      ↓
Closures
      ↓
Functional Patterns
      ↓
⭐ Design Patterns
```

Design patterns are reusable solutions to **common software design problems**.

---

# 1. What Is a Design Pattern?

A design pattern is not a library or framework.

It is a **proven way of structuring code**.

Imagine you repeatedly encounter this problem:

> "My application needs to create different types of objects, but I don't want my business code to know which concrete class to instantiate."

A **Factory Pattern** can solve that.

Another problem:

> "I have multiple ways to calculate payment, shipping, discounts, etc."

A **Strategy Pattern** can solve that.

So:

```text
Problem
   ↓
Common solution
   ↓
Design Pattern
```

---

# 2. Real-World Analogy

Imagine a restaurant.

You don't walk into the kitchen and personally:

```text
buy ingredients
↓
prepare food
↓
operate oven
↓
wash dishes
```

Instead, responsibilities are separated:

```text
Customer
   ↓
Waiter
   ↓
Kitchen
   ↓
Chef
   ↓
Food
```

Good software architecture works similarly.

Each class should have a **clear responsibility**.

---

# 3. Why Patterns Matter

Without patterns, applications often become:

```text
Controller
   ↓
1000 lines of logic
   ↓
Database
   ↓
Payment
   ↓
Email
   ↓
API
   ↓
File storage
```

That's difficult to maintain.

With proper design:

```text
Controller
   ↓
Service
   ↓
Strategy / Repository / Factory
   ↓
Infrastructure
```

Each component has a focused responsibility.

---

# 4. Pattern #1 — Factory

Let's start with **Factory**, because you'll encounter it frequently in PHP and Laravel.

Suppose we support:

```text
Stripe
PayPal
Bank Transfer
```

A naive implementation might be:

```php
if ($method === 'stripe') {
    $payment = new StripePayment();
} elseif ($method === 'paypal') {
    $payment = new PayPalPayment();
} elseif ($method === 'bank') {
    $payment = new BankPayment();
}
```

This works.

But imagine 20 payment methods.

Your code becomes difficult to maintain.

---

# 5. Factory Solution

First create an interface:

```php
<?php

declare(strict_types=1);

interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then implementations:

```php
final class StripePayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        echo "Charging {$amount} through Stripe";

        return true;
    }
}
```

```php
final class PayPalPayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        echo "Charging {$amount} through PayPal";

        return true;
    }
}
```

Now create the Factory:

```php
final class PaymentGatewayFactory
{
    public function create(string $method): PaymentGateway
    {
        return match ($method) {
            'stripe' => new StripePayment(),
            'paypal' => new PayPalPayment(),
            default => throw new InvalidArgumentException(
                "Unsupported payment method: {$method}"
            ),
        };
    }
}
```

Usage:

```php
$factory = new PaymentGatewayFactory();

$gateway = $factory->create('stripe');

$gateway->charge(100);
```

The caller doesn't need to know:

```php
new StripePayment()
```

or:

```php
new PayPalPayment()
```

The Factory handles creation.

---

# 6. Factory Architecture

```text
Application
    │
    ▼
PaymentGatewayFactory
    │
    ├── StripePayment
    ├── PayPalPayment
    └── BankPayment
```

The important part is:

```text
Caller
  ↓
Factory
  ↓
Interface
  ↓
Concrete implementation
```

---

# 7. Why Interface + Factory?

Because the application depends on an abstraction:

```php
PaymentGateway
```

instead of:

```php
StripePayment
```

That's directly related to the **Dependency Inversion Principle** you learned earlier.

---

# 8. Laravel Factory

Laravel itself uses factories heavily.

For example, Laravel model factories:

```php
User::factory()->create();
```

Conceptually:

```text
User Factory
     ↓
User object
     ↓
Database
```

Factories are particularly useful when:

- creating objects is complicated
- multiple implementations exist
- construction logic should be centralized
- tests need different implementations

---

# 9. Pattern #2 — Strategy

Factory answers:

> **Which object should I create?**

Strategy answers:

> **Which algorithm/behavior should I use?**

This distinction is very important.

---

## Example: Shipping

Suppose your application supports:

```text
Standard Shipping
Express Shipping
International Shipping
```

Create an interface:

```php
interface ShippingStrategy
{
    public function calculate(float $weight): float;
}
```

Standard:

```php
final class StandardShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 5;
    }
}
```

Express:

```php
final class ExpressShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 10;
    }
}
```

International:

```php
final class InternationalShipping implements ShippingStrategy
{
    public function calculate(float $weight): float
    {
        return $weight * 20;
    }
}
```

Now create a service:

```php
final class ShippingCalculator
{
    public function __construct(
        private ShippingStrategy $strategy
    ) {
    }

    public function calculate(float $weight): float
    {
        return $this->strategy->calculate($weight);
    }
}
```

Usage:

```php
$calculator = new ShippingCalculator(
    new ExpressShipping()
);

$cost = $calculator->calculate(5);
```

---

# 10. Strategy Pattern Diagram

```text
              ShippingStrategy
                     ▲
          ┌──────────┼──────────┐
          │          │          │
       Standard    Express   International
          │          │          │
          └──────────┼──────────┘
                     │
              ShippingCalculator
```

The calculator doesn't care **how** shipping is calculated.

It only knows:

```php
$strategy->calculate($weight);
```

That's polymorphism.

---

# 11. Strategy in Laravel

Imagine an e-commerce application:

```text
Discount
 ├── PercentageDiscount
 ├── FixedDiscount
 ├── BlackFridayDiscount
 └── VIPDiscount
```

Instead of:

```php
if ($type === 'percentage') {
    ...
} elseif ($type === 'fixed') {
    ...
} elseif ($type === 'black_friday') {
    ...
}
```

you can use:

```php
interface DiscountStrategy
{
    public function calculate(float $price): float;
}
```

Then each discount algorithm gets its own class.

This makes adding a new discount much safer.

---

# 12. Factory vs Strategy

This is an important interview question.

| Factory                 | Strategy                    |
| ----------------------- | --------------------------- |
| Creates objects         | Selects behavior            |
| Focuses on construction | Focuses on algorithms       |
| `create()`              | `calculate()` / `execute()` |
| "Which object?"         | "Which behavior?"           |

For example:

```text
Factory:
"Which payment gateway should I create?"

Strategy:
"How should this payment be processed?"
```

You can even use both together:

```text
Factory
   ↓
Payment Strategy
   ↓
Payment Gateway
```

---

# 13. Pattern #3 — Adapter

Suppose your application expects:

```php
interface SmsSender
{
    public function send(string $phone, string $message): void;
}
```

But an external SMS provider gives you:

```php
$provider->sendMessage(
    $number,
    $text
);
```

The interfaces don't match.

Instead of changing your application everywhere, create an adapter.

```php
final class SmsProviderAdapter implements SmsSender
{
    public function __construct(
        private ExternalSmsProvider $provider
    ) {
    }

    public function send(
        string $phone,
        string $message
    ): void {
        $this->provider->sendMessage(
            $phone,
            $message
        );
    }
}
```

Now:

```text
Your Application
       ↓
    SmsSender
       ↓
SmsProviderAdapter
       ↓
External Provider
```

The Adapter translates one interface into another.

---

# 14. Real Laravel Example

Imagine you initially use:

```text
Twilio
```

and later want:

```text
Vonage
```

Your application shouldn't contain:

```php
Twilio::send(...)
```

everywhere.

Instead:

```php
interface SmsSender
{
    public function send(
        string $phone,
        string $message
    ): void;
}
```

Then:

```text
TwilioSmsSender
VonageSmsSender
```

both implement the same interface.

Your business logic becomes provider-independent.

---

# 15. Pattern #4 — Repository

A Repository provides an abstraction around data access.

Example:

```php
interface UserRepository
{
    public function findById(int $id): ?User;
}
```

Implementation:

```php
final class EloquentUserRepository implements UserRepository
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }
}
```

Then:

```php
final class UserService
{
    public function __construct(
        private UserRepository $users
    ) {
    }

    public function findUser(int $id): ?User
    {
        return $this->users->findById($id);
    }
}
```

Architecture:

```text
Controller
    ↓
UserService
    ↓
UserRepository
    ↓
Eloquent
    ↓
Database
```

---

# 16. Important Laravel Warning ⚠️

Don't automatically create repositories for **every Eloquent model**.

This:

```text
UserRepository
PostRepository
OrderRepository
ProductRepository
...
```

can become unnecessary abstraction.

If your repository simply does:

```php
return User::find($id);
```

and adds no meaningful behavior, you may be creating complexity without benefit.

### Use a repository when it provides real value.

For example:

- complicated queries
- multiple data sources
- external APIs + database
- meaningful data-access abstraction
- testing requirements
- domain-specific querying

---

# 17. Pattern #5 — Decorator

Decorator allows you to add behavior without modifying the original class.

Suppose:

```php
interface PaymentProcessor
{
    public function process(float $amount): bool;
}
```

Base implementation:

```php
final class StripeProcessor implements PaymentProcessor
{
    public function process(float $amount): bool
    {
        return true;
    }
}
```

Now we want logging.

Instead of modifying Stripe:

```php
final class LoggingPaymentProcessor implements PaymentProcessor
{
    public function __construct(
        private PaymentProcessor $processor
    ) {
    }

    public function process(float $amount): bool
    {
        logger()->info('Processing payment', [
            'amount' => $amount,
        ]);

        return $this->processor->process($amount);
    }
}
```

Now:

```php
$processor = new LoggingPaymentProcessor(
    new StripeProcessor()
);
```

Architecture:

```text
LoggingPaymentProcessor
          ↓
   StripeProcessor
          ↓
      Payment
```

You can stack decorators:

```text
Logging
   ↓
Metrics
   ↓
Caching
   ↓
Stripe
```

---

# 18. Laravel Dependency Injection

Laravel's service container makes these patterns especially powerful.

For example:

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }
}
```

Laravel can resolve the dependency through its container when the binding is configured.

Conceptually:

```text
OrderService
     ↓
PaymentGateway
     ↓
StripePayment
```

This means your business logic depends on:

```php
PaymentGateway
```

not:

```php
StripePayment
```

That gives you flexibility.

---

# 19. How These Patterns Work Together

A realistic Laravel application could look like:

```text
                    Controller
                        │
                        ▼
                   OrderService
                        │
          ┌─────────────┼─────────────┐
          ▼             ▼             ▼
     Repository       Factory       Event
          │             │
          ▼             ▼
       Eloquent      Strategy
                        │
                ┌───────┼───────┐
                ▼       ▼       ▼
              Stripe  PayPal   Bank
```

This is much closer to production architecture.

---

# 20. Don't Use Patterns Just to Look Advanced

This is extremely important.

Bad developer thinking:

> "I know Factory, so everything needs a Factory."

No.

Patterns solve problems.

If you have:

```php
$user = new User();
```

you don't need:

```text
UserFactoryFactory
```

😂

Keep simple code simple.

---

# 21. Common Mistakes

### ❌ Mistake 1 — Pattern overengineering

Don't introduce five interfaces for a two-line operation.

---

### ❌ Mistake 2 — Repository everywhere

Eloquent already provides a powerful data-access layer.

---

### ❌ Mistake 3 — Giant Factory

If your Factory contains 100 `case` branches, consider a better registration/resolution design.

---

### ❌ Mistake 4 — Strategy without meaningful variation

If every strategy contains identical logic, you probably don't need Strategy.

---

### ❌ Mistake 5 — Business logic inside Controller

Avoid:

```php
public function store(Request $request)
{
    // 200 lines
    // payment
    // inventory
    // discounts
    // emails
}
```

Prefer:

```php
public function store(StoreOrderRequest $request)
{
    $order = $this->orderService->create(
        $request->validated()
    );

    return response()->json($order);
}
```

---

# 22. Performance

Design patterns usually have a very small runtime cost compared with database/network operations.

The bigger concern is:

> **Complexity.**

Bad architecture can cost more developer time than CPU time.

For example:

```text
100 unnecessary classes
      ↓
Harder debugging
      ↓
Harder onboarding
      ↓
More maintenance
```

Optimize architecture for **clarity and changeability**, not merely fewer CPU instructions.

---

# 23. Security

Patterns don't automatically make applications secure.

For example, a Factory should not blindly instantiate arbitrary classes from user input.

Avoid something conceptually like:

```php
$class = $_POST['gateway'];

return new $class();
```

User input should never freely control which PHP class gets instantiated.

Instead, whitelist supported options:

```php
return match ($method) {
    'stripe' => new StripePayment(),
    'paypal' => new PayPalPayment(),
    default => throw new InvalidArgumentException(),
};
```

---

# 24. Interview Questions

### Q1. What is a design pattern?

A reusable solution to a common software design problem.

### Q2. Factory vs Strategy?

Factory handles **object creation**.

Strategy handles **interchangeable behavior/algorithms**.

### Q3. What problem does Adapter solve?

It allows incompatible interfaces to work together.

### Q4. What is Repository?

An abstraction around data-access logic.

### Q5. Should every Laravel model have a Repository?

**No.** Only when the abstraction provides meaningful value.

### Q6. Why use interfaces?

To depend on abstractions rather than concrete implementations.

### Q7. Which SOLID principle connects strongly with Strategy?

**Open/Closed Principle** and **Dependency Inversion Principle** are especially relevant.

---

# 25. Small Exercise

Build a **Notification System**.

Requirements:

```text
Email
SMS
Push Notification
```

Create:

```php
interface NotificationSender
{
    public function send(
        string $recipient,
        string $message
    ): bool;
}
```

Then implement:

```text
EmailNotification
SmsNotification
PushNotification
```

Finally create a Factory:

```php
NotificationFactory
```

It should support:

```php
$factory->create('email');
$factory->create('sms');
$factory->create('push');
```

All returned objects must implement:

```php
NotificationSender
```

### Architecture you should achieve

```text
NotificationSender
        ▲
        │
 ┌──────┼─────────┐
 │      │         │
Email   SMS      Push
        ▲
        │
NotificationFactory
```

---

# 26. Challenge 🔥

Build a **Payment System** using both **Factory + Strategy**.

Requirements:

```text
Payment methods:
- Stripe
- PayPal
- Bank Transfer

Discount types:
- Percentage
- Fixed
```

Architecture:

```text
OrderService
    │
    ├── PaymentGatewayFactory
    │          │
    │          ├── Stripe
    │          ├── PayPal
    │          └── Bank
    │
    └── DiscountStrategy
               │
               ├── Percentage
               └── Fixed
```

Your `OrderService` should **not** contain:

```php
if ($paymentMethod === ...)
```

or:

```php
if ($discountType === ...)
```

That is your first serious architecture exercise.

---

# 27. Summary

Today you learned five important patterns:

```text
Factory
   ↓
Creates objects

Strategy
   ↓
Changes behavior/algorithm

Adapter
   ↓
Connects incompatible interfaces

Repository
   ↓
Abstracts data access

Decorator
   ↓
Adds behavior around an object
```

And the bigger picture:

```text
             SOLID
               ↓
        Design Patterns
               ↓
      Clean Architecture
               ↓
      Laravel Applications
               ↓
     Production Backend
```

The key principle to remember:

> **A design pattern is a tool, not a requirement. Use it when it makes the design simpler, more flexible, or easier to maintain.**

I can give you a three-question practice quiz here to test whether you can distinguish **Factory, Strategy, Adapter, Repository, and Decorator** before we move to the next lesson.
