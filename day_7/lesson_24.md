# Lesson 24 — Dependency Injection & Laravel Service Container

This is one of the **most important lessons for becoming a professional Laravel backend engineer**.

You already learned interfaces, SOLID, Factory, Strategy, and other design patterns.

Now we're going to connect them to one of Laravel's most important mechanisms:

> **Dependency Injection (DI) + Service Container**

---

# 1. What Is a Dependency?

A dependency is simply something a class **needs in order to do its job**.

For example:

```php
final class OrderService
{
    public function create(): void
    {
        // Need payment service
        // Need email service
        // Need inventory service
    }
}
```

The `OrderService` depends on those other services.

Think:

```text
OrderService
   │
   ├── PaymentService
   ├── InventoryService
   └── NotificationService
```

Those are its **dependencies**.

---

# 2. The Bad Way — Creating Dependencies Yourself

Consider:

```php
final class OrderService
{
    public function create(): void
    {
        $payment = new StripePayment();

        $payment->charge(100);
    }
}
```

It works.

But there is a problem.

`OrderService` is now tightly coupled to:

```php
StripePayment
```

If tomorrow you switch to PayPal:

```php
$payment = new PayPalPayment();
```

you have to modify `OrderService`.

That's not ideal.

---

# 3. Dependency Injection

Instead, provide the dependency from outside.

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }

    public function create(): void
    {
        $this->paymentGateway->charge(100);
    }
}
```

Now:

```text
OrderService
     │
     ↓
PaymentGateway
     ↑
     │
 ┌───┴────┐
 │        │
Stripe   PayPal
```

`OrderService` doesn't care whether the implementation is Stripe or PayPal.

This is **Dependency Injection**.

---

# 4. Real-World Analogy

Imagine you have a laptop.

The laptop needs electricity.

You don't build a power plant inside your laptop.

Instead:

```text
Laptop
   ↓
Power socket
   ↓
Electricity
```

The laptop depends on **electricity**, but it doesn't need to know how electricity was generated.

Similarly:

```text
OrderService
      ↓
PaymentGateway
      ↓
StripePayment
```

The service depends on an **abstraction**, not the implementation.

---

# 5. Constructor Injection

This is the preferred form of dependency injection in Laravel.

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }
}
```

Why constructor?

Because the dependency is required for the class to function.

The object cannot properly exist without it.

---

# 6. Method Injection

You can also inject dependencies into a method:

```php
public function process(
    PaymentGateway $paymentGateway
): void {
    $paymentGateway->charge(100);
}
```

Useful when the dependency is needed only for one specific operation.

But for a service's core dependencies:

> Prefer constructor injection.

---

# 7. Dependency Injection vs Dependency Inversion

These are related but different concepts.

### Dependency Injection

A technique:

> "Give an object the dependencies it needs."

### Dependency Inversion Principle

A SOLID principle:

> "High-level code should depend on abstractions rather than concrete implementations."

Example:

```php
private PaymentGateway $paymentGateway;
```

rather than:

```php
private StripePayment $paymentGateway;
```

---

# 8. Raw PHP Example

Let's build a simple payment system.

### Interface

```php
<?php

declare(strict_types=1);

interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

### Stripe

```php
final class StripePayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        echo "Stripe charged {$amount}";

        return true;
    }
}
```

### Order Service

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }

    public function createOrder(float $amount): bool
    {
        return $this->paymentGateway->charge($amount);
    }
}
```

Now:

```php
$payment = new StripePayment();

$orderService = new OrderService($payment);

$orderService->createOrder(100);
```

Notice what happened:

```text
You
 ↓
StripePayment
 ↓
OrderService
```

You manually supplied the dependency.

---

# 9. What Does Laravel Add?

Laravel provides the:

> **Service Container**

The container manages object creation and dependencies for you.

Conceptually:

```text
Laravel Application
        │
        ▼
 Service Container
        │
        ├── OrderService
        ├── PaymentGateway
        ├── StripePayment
        ├── UserService
        └── NotificationService
```

Instead of manually doing:

```php
$payment = new StripePayment();

$orderService = new OrderService($payment);
```

Laravel can resolve the dependency graph.

---

# 10. Laravel Automatic Resolution

Suppose:

```php
final class StripePayment implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
```

and:

```php
final class OrderService
{
    public function __construct(
        private StripePayment $payment
    ) {
    }
}
```

Laravel can often automatically instantiate:

```text
OrderService
     ↓
StripePayment
```

because it can inspect the constructor.

This is called **automatic resolution**.

---

# 11. But What About Interfaces?

This is where the Service Container becomes really important.

Suppose:

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }
}
```

Laravel sees:

```text
PaymentGateway
```

But that's an interface.

Laravel cannot simply do:

```php
new PaymentGateway()
```

because interfaces cannot be instantiated.

So you tell Laravel:

```text
"When someone asks for PaymentGateway,
give them StripePayment."
```

---

# 12. Binding an Interface

In Laravel, you can bind the interface to an implementation.

Conceptually:

```php
$this->app->bind(
    PaymentGateway::class,
    StripePayment::class
);
```

Now:

```text
OrderService
      ↓
PaymentGateway
      ↓
StripePayment
```

When Laravel needs:

```php
PaymentGateway
```

it resolves:

```php
StripePayment
```

---

# 13. Why This Is Powerful

Later you could change:

```php
$this->app->bind(
    PaymentGateway::class,
    PayPalPayment::class
);
```

Your `OrderService` does not change.

That's the power of depending on abstractions.

---

# 14. Laravel Service Provider

Bindings are commonly registered in a **Service Provider**.

For example:

```php
final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PaymentGateway::class,
            StripePayment::class
        );
    }
}
```

The important distinction:

### `register()`

Used primarily for registering services/bindings in the container.

### `boot()`

Used for logic that should run after services have been registered.

For example:

```php
public function boot(): void
{
    // Initialization logic
}
```

---

# 15. Request Lifecycle Connection

You previously learned about the Laravel request lifecycle.

Now connect it with DI:

```text
HTTP Request
     ↓
Laravel Application
     ↓
Router
     ↓
Controller
     ↓
Service Container
     ↓
Resolve Controller
     ↓
Resolve Dependencies
     ↓
Service
     ↓
Repository / Model
     ↓
Database
```

For example:

```php
final class OrderController
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    public function store(): Response
    {
        $this->orderService->createOrder();

        // ...
    }
}
```

Laravel resolves:

```text
OrderController
       ↓
OrderService
       ↓
PaymentGateway
       ↓
StripePayment
```

You don't manually construct the whole dependency tree.

---

# 16. Dependency Graph

This is a very important concept.

Imagine:

```text
OrderController
       │
       ▼
OrderService
       │
       ├──────────────┐
       ▼              ▼
PaymentGateway   OrderRepository
       │              │
       ▼              ▼
StripePayment     Eloquent
```

The Service Container manages this graph.

This is why it's called a **container**.

---

# 17. `bind()` vs `singleton()`

You'll frequently encounter these.

### `bind()`

Creates/resolves a normal binding.

Conceptually:

```php
$this->app->bind(
    PaymentGateway::class,
    StripePayment::class
);
```

### `singleton()`

The container keeps the same instance for the container lifecycle.

```php
$this->app->singleton(
    PaymentGateway::class,
    StripePayment::class
);
```

Think:

```text
bind()
Request resolution
   ↓
potentially new instance

singleton()
   ↓
same container-managed instance
```

Don't use `singleton()` just because it sounds faster.

The lifecycle and state of the service matter.

---

# 18. Contextual Binding

Sometimes different classes need different implementations.

For example:

```text
OrderService → Stripe
SubscriptionService → PayPal
```

Both depend on:

```php
PaymentGateway
```

but need different implementations.

Laravel supports **contextual binding** for this kind of situation.

Conceptually:

```text
OrderService
    ↓
StripePayment

SubscriptionService
    ↓
PayPalPayment
```

This is an advanced Service Container feature you'll use when the application genuinely requires it.

---

# 19. Laravel Controllers + DI

A professional controller should generally be thin.

For example:

```php
final class OrderController
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    public function store(
        StoreOrderRequest $request
    ): JsonResponse {
        $order = $this->orderService->create(
            $request->validated()
        );

        return response()->json($order);
    }
}
```

Notice what the controller **doesn't** contain:

```text
❌ payment logic
❌ discount calculation
❌ inventory logic
❌ database transaction logic
❌ email logic
```

Those belong in appropriate application/domain services.

---

# 20. Service Container vs Service Class

These are **not the same thing**.

### Service Class

Contains application/business logic.

Example:

```php
OrderService
PaymentService
InventoryService
```

### Service Container

Manages dependencies and object resolution.

Example:

```text
Laravel Container
       ↓
OrderService
       ↓
PaymentGateway
```

Don't confuse them.

---

# 21. Dependency Injection Makes Testing Easier

This is one of the biggest benefits.

Suppose:

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }
}
```

In production:

```text
PaymentGateway
      ↓
StripePayment
```

In a test:

```text
PaymentGateway
      ↓
FakePaymentGateway
```

You don't need to contact Stripe.

Example:

```php
final class FakePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
```

Then:

```php
$service = new OrderService(
    new FakePaymentGateway()
);
```

This is a huge advantage of loose coupling.

---

# 22. Dependency Injection vs `new`

Compare:

### Tight coupling

```php
final class OrderService
{
    public function create(): void
    {
        $payment = new StripePayment();

        $payment->charge(100);
    }
}
```

### Dependency Injection

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway
    ) {
    }

    public function create(): void
    {
        $this->paymentGateway->charge(100);
    }
}
```

The second design is much easier to:

- test
- replace
- extend
- maintain

---

# 23. Common Mistakes

### ❌ 1. Injecting concrete classes everywhere

Prefer:

```php
PaymentGateway
```

when an abstraction is genuinely useful.

Not:

```php
StripePayment
```

everywhere.

---

### ❌ 2. Creating dependencies inside services

Avoid:

```php
$this->payment = new StripePayment();
```

when the dependency should be injected.

---

### ❌ 3. Making everything an interface

This is also bad.

You don't need:

```text
UserInterface
UserServiceInterface
UserRepositoryInterface
UserFactoryInterface
...
```

for every class.

Use abstractions where they provide real flexibility.

---

### ❌ 4. Using Service Container as a global service locator

Avoid doing this everywhere:

```php
app(OrderService::class);
```

when constructor injection would work.

Prefer:

```php
public function __construct(
    private OrderService $orderService
) {
}
```

Constructor injection makes dependencies visible.

---

# 24. Performance

Dependency Injection itself is generally not your application's major performance concern.

Your expensive operations are usually:

```text
Database
   ↓
Network
   ↓
External API
   ↓
File system
```

Don't sacrifice clean architecture to avoid a tiny DI/container overhead.

However:

- avoid unnecessarily creating huge dependency graphs
- don't make every class a singleton
- don't resolve services repeatedly when unnecessary
- keep services focused

---

# 25. Security

DI doesn't automatically make your application secure.

But it can help security through controlled dependencies.

For example, instead of allowing arbitrary payment providers:

```php
$class = $request->input('payment_class');

new $class();
```

use controlled container bindings and explicit implementations.

Never allow untrusted input to decide which PHP class gets instantiated.

---

# 26. Interview Questions

### Q1. What is Dependency Injection?

Providing an object's dependencies from outside instead of having the object create them itself.

### Q2. Why is constructor injection preferred?

Because required dependencies are explicit and the object can be created in a valid state.

### Q3. What is Laravel's Service Container?

A system that manages dependency resolution and object creation.

### Q4. Can Laravel resolve concrete classes automatically?

Often yes, when their dependencies are themselves resolvable.

### Q5. Can Laravel instantiate an interface automatically?

No. It needs a binding telling the container which implementation to use.

### Q6. What is `bind()`?

Registers a container binding.

### Q7. What is `singleton()`?

Registers a binding where the container reuses the same resolved instance during its lifecycle.

### Q8. Why use interfaces?

To reduce coupling and allow implementations to be replaced.

---

# 27. Small Exercise 📝

Create:

```php
interface Logger
{
    public function log(string $message): void;
}
```

Then:

```php
final class FileLogger implements Logger
{
    public function log(string $message): void
    {
        // ...
    }
}
```

And:

```php
final class UserService
{
    public function __construct(
        private Logger $logger
    ) {
    }

    public function createUser(): void
    {
        $this->logger->log('User created');
    }
}
```

Your goal:

```text
UserService
     ↓
   Logger
     ↓
 FileLogger
```

Then create:

```php
DatabaseLogger
```

and replace `FileLogger` without modifying `UserService`.

---

# 28. Challenge 🔥

Build this architecture:

```text
OrderController
       ↓
OrderService
       ↓
PaymentGateway
       ↓
StripePayment
```

Requirements:

### Interface

```php
PaymentGateway
```

### Implementations

```text
StripePayment
PayPalPayment
```

### Service

```text
OrderService
```

### Controller

```text
OrderController
```

The Controller must **not** do:

```php
new StripePayment();
```

The `OrderService` must **not** do:

```php
new StripePayment();
```

Instead:

```text
Laravel Container
       ↓
PaymentGateway
       ↓
StripePayment
```

Then change the binding to PayPal.

Your `OrderService` should require **zero code changes**.

---

# 29. The Big Picture

You've now connected several concepts:

```text
Interface
    ↓
Dependency Inversion
    ↓
Dependency Injection
    ↓
Service Container
    ↓
Service Provider
    ↓
Laravel Application
```

And this is the architecture you're aiming for:

```text
                 HTTP Request
                      │
                      ▼
                  Controller
                      │
                      ▼
                   Service
                      │
              ┌───────┴────────┐
              ▼                ▼
          Repository       Strategy
              │                │
              ▼                ▼
           Eloquent        Interface
              │                │
              ▼                ▼
           Database       Implementation
```

### ⭐ Professional rule

> **Make dependencies explicit, depend on abstractions where useful, inject dependencies rather than constructing them inside business logic, and let Laravel's container manage the dependency graph.**

---

## Next Lesson → Laravel Architecture: Service Layer + Repository + DTO

We'll take everything you've learned so far and build a **real Todo application architecture**, moving from:

```text
Controller → Model
```

to:

```text
Request
  ↓
Form Request
  ↓
Controller
  ↓
Service
  ↓
DTO
  ↓
Repository
  ↓
Eloquent
  ↓
Database
```

That will be your first step toward writing Laravel code that looks like a **professional production backend**, rather than tutorial-level Laravel.
