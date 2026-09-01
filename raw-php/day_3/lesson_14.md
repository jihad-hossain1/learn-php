# 🚀 Module 2 — Lesson 14: SOLID Principles

Today we move from **"I can write OOP code"** to **"I can design maintainable OOP code."**

SOLID is a set of five design principles:

```text
S → Single Responsibility Principle
O → Open/Closed Principle
L → Liskov Substitution Principle
I → Interface Segregation Principle
D → Dependency Inversion Principle
```

These principles are especially important in **Laravel applications**, because large applications can become difficult to maintain if responsibilities and dependencies are poorly designed.

---

# 1. Why SOLID?

Imagine you have one Laravel controller:

```php
class OrderController
{
    public function store(Request $request)
    {
        // Validate request

        // Create order

        // Calculate price

        // Process payment

        // Send email

        // Update inventory

        // Generate invoice

        // Save everything
    }
}
```

It might work.

But eventually this controller becomes:

```text
OrderController
   │
   ├── Validation
   ├── Business Logic
   ├── Payment
   ├── Inventory
   ├── Email
   ├── Invoice
   └── Database
```

Now changing payment logic could break the controller.

Testing becomes difficult.

Understanding the code becomes difficult.

SOLID helps us move toward:

```text
Controller
    ↓
OrderService
    ├── PaymentGateway
    ├── InventoryService
    └── NotificationService
```

Each component has a clearer responsibility.

---

# 2. S — Single Responsibility Principle

## Definition

> A class should have one responsibility and one reason to change.

This doesn't necessarily mean:

> "A class can only have one method."

It means the class should have **one cohesive purpose**.

---

# 3. Bad Example

Imagine:

```php
class OrderService
{
    public function createOrder(): void
    {
        // Create order
    }

    public function sendEmail(): void
    {
        // Send email
    }

    public function generateInvoice(): void
    {
        // Generate PDF
    }

    public function processPayment(): void
    {
        // Process payment
    }
}
```

This class is responsible for:

```text
Order
Email
Invoice
Payment
```

That's too many responsibilities.

---

# 4. Better Design

Separate responsibilities:

```php
final class OrderService
{
    public function createOrder(): void
    {
        // Order business logic
    }
}
```

```php
final class PaymentService
{
    public function processPayment(): void
    {
        // Payment logic
    }
}
```

```php
final class InvoiceService
{
    public function generateInvoice(): void
    {
        // Invoice logic
    }
}
```

```php
final class NotificationService
{
    public function sendOrderConfirmation(): void
    {
        // Notification logic
    }
}
```

Now:

```text
OrderService
    ↓
Orders

PaymentService
    ↓
Payments

InvoiceService
    ↓
Invoices

NotificationService
    ↓
Notifications
```

Much easier to maintain.

---

# 5. Real Laravel Example

Instead of:

```php
class OrderController
{
    public function store()
    {
        // 200 lines of business logic
    }
}
```

Prefer:

```php
final class OrderController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create(
            $request->validated()
        );

        return response()->json($order);
    }
}
```

The controller's responsibility is primarily:

```text
HTTP request
    ↓
Controller
    ↓
Service
    ↓
Response
```

The business logic belongs elsewhere.

---

# 6. Why SRP Helps

Without SRP:

```text
One class
   ↓
Everything
   ↓
Hard to test
   ↓
Hard to change
   ↓
High risk of bugs
```

With SRP:

```text
Small responsibilities
        ↓
Low coupling
        ↓
Easier testing
        ↓
Easier maintenance
```

---

# 7. O — Open/Closed Principle

The Open/Closed Principle says:

> Software entities should be open for extension but closed for modification.

In simpler words:

> You should be able to add new behavior without constantly modifying existing working code.

---

# 8. Bad Example

Imagine:

```php
class PaymentService
{
    public function pay(string $method, float $amount): void
    {
        if ($method === 'stripe') {
            // Stripe payment
        } elseif ($method === 'paypal') {
            // PayPal payment
        } elseif ($method === 'cash') {
            // Cash payment
        }
    }
}
```

Today:

```text
stripe
paypal
cash
```

Tomorrow:

```text
bank
mobile_money
crypto
```

You have to modify the same class again and again.

---

# 9. Better Design

Create an interface:

```php
interface PaymentGateway
{
    public function charge(float $amount): void;
}
```

Then implementations:

```php
final class StripePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): void
    {
        // Stripe implementation
    }
}
```

```php
final class PayPalPaymentGateway implements PaymentGateway
{
    public function charge(float $amount): void
    {
        // PayPal implementation
    }
}
```

Now adding another payment provider means creating another class:

```php
final class BankPaymentGateway implements PaymentGateway
{
    public function charge(float $amount): void
    {
        // Bank implementation
    }
}
```

Existing payment implementations don't need to be modified.

That's the idea behind Open/Closed.

---

# 10. L — Liskov Substitution Principle

This one sounds complicated, but the core idea is simple.

> If class B is a subtype of class A, you should be able to use B anywhere A is expected without breaking the program.

Example:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then:

```php
class StripePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
```

And:

```php
class PayPalPaymentGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
```

Both can be used here:

```php
function checkout(PaymentGateway $gateway): bool
{
    return $gateway->charge(500);
}
```

So:

```php
checkout(new StripePaymentGateway());
checkout(new PayPalPaymentGateway());
```

works correctly.

---

# 11. LSP Violation Example

Suppose:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then someone creates:

```php
class FakePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        throw new RuntimeException('I cannot charge.');
    }
}
```

Technically it implements the interface.

But if the application expects:

```php
checkout($gateway);
```

to perform a charge, this implementation breaks that expectation.

So simply implementing an interface isn't enough.

The implementation must honor the **behavioral contract**.

---

# 12. Another Classic Example

Imagine:

```php
class Bird
{
    public function fly(): void
    {
        // Fly
    }
}
```

Then:

```php
class Penguin extends Bird
{
    public function fly(): void
    {
        throw new RuntimeException('Penguins cannot fly.');
    }
}
```

Now:

```php
function makeBirdFly(Bird $bird): void
{
    $bird->fly();
}
```

This breaks when given:

```php
makeBirdFly(new Penguin());
```

The inheritance relationship was poorly designed.

The lesson:

> Don't create inheritance relationships that violate the expected behavior of the parent type.

---

# 13. I — Interface Segregation Principle

The Interface Segregation Principle says:

> Clients should not be forced to depend on methods they don't need.

In simple language:

> Prefer small, focused interfaces instead of huge interfaces.

---

# 14. Bad Interface

Imagine:

```php
interface UserService
{
    public function create(): void;

    public function update(): void;

    public function delete(): void;

    public function sendEmail(): void;

    public function generateReport(): void;

    public function exportCsv(): void;
}
```

A class might only need:

```text
create()
update()
```

but is forced to implement everything.

That's not ideal.

---

# 15. Better Interfaces

Split them:

```php
interface UserCreator
{
    public function create(): void;
}
```

```php
interface UserUpdater
{
    public function update(): void;
}
```

```php
interface UserDeleter
{
    public function delete(): void;
}
```

Now classes depend only on what they need.

```text
UserCreator
UserUpdater
UserDeleter
     ↓
Small focused contracts
```

---

# 16. Why Small Interfaces Are Useful

Suppose your payment application needs only:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Don't unnecessarily add:

```php
refund()
cancel()
capture()
authorize()
saveCard()
deleteCard()
getTransactions()
...
```

unless every implementation genuinely needs those capabilities.

Smaller contracts are usually easier to:

* Understand
* Implement
* Test
* Replace

---

# 17. D — Dependency Inversion Principle

This is one of the most important SOLID principles for Laravel.

The principle says:

> High-level modules should not depend directly on low-level modules. Both should depend on abstractions.

In simpler terms:

**Don't tightly couple your business logic to a specific implementation when an abstraction makes sense.**

---

# 18. Bad Example

```php
class OrderService
{
    public function checkout(float $amount): void
    {
        $stripe = new StripePaymentGateway();

        $stripe->charge($amount);
    }
}
```

Problem:

```text
OrderService
     │
     └──────> StripePaymentGateway
```

`OrderService` is directly coupled to Stripe.

---

# 19. Better Example

Create an abstraction:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then:

```php
final class OrderService
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

Now:

```text
             PaymentGateway
                  ▲
                  │
          ┌───────┴───────┐
          │               │
       Stripe           PayPal
          ▲               ▲
          └───────┬───────┘
                  │
             OrderService
```

The important point is that `OrderService` depends on:

```php
PaymentGateway
```

not:

```php
StripePaymentGateway
```

---

# 20. Dependency Injection vs Dependency Inversion

Don't confuse these.

### Dependency Injection

A technique:

```php
public function __construct(
    private PaymentGateway $gateway,
) {
}
```

The dependency is injected into the class.

### Dependency Inversion

A design principle:

```text
Business logic
      ↓
Abstraction
      ↑
Concrete implementation
```

Dependency injection is one way to implement dependency inversion.

---

# 21. SOLID Together

Now let's put all five together.

Imagine an e-commerce checkout.

```text
                    Controller
                        │
                        ▼
                  CheckoutService
                        │
              ┌─────────┼──────────┐
              ▼         ▼          ▼
        PaymentGateway Inventory Notification
              │
        ┌─────┴─────┐
        ▼           ▼
     Stripe       PayPal
```

### S — Single Responsibility

Each class has a focused responsibility.

### O — Open/Closed

Add a new payment gateway without rewriting checkout logic.

### L — Liskov Substitution

Any valid `PaymentGateway` implementation should work correctly.

### I — Interface Segregation

Use focused contracts rather than giant interfaces.

### D — Dependency Inversion

`CheckoutService` depends on `PaymentGateway`, not Stripe directly.

---

# 22. Laravel Connection

These principles appear throughout Laravel development.

For example:

```php
final class OrderController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }
}
```

Controller → Service:

```text
D + SRP
```

And:

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway,
    ) {
    }
}
```

Service → Interface:

```text
D + DI + loose coupling
```

And different implementations:

```text
PaymentGateway
   ├── Stripe
   ├── PayPal
   └── Bank
```

supports:

```text
O + L
```

---

# 23. Important Warning: Don't Overengineer

SOLID does **not** mean:

```text
Every class
   ↓
Interface
   ↓
Repository
   ↓
Service
   ↓
Factory
   ↓
Manager
   ↓
Adapter
   ↓
Strategy
```

for every tiny feature.

That's overengineering.

For a simple operation, this may be enough:

```php
final class UserService
{
    public function createUser(): User
    {
        // ...
    }
}
```

Don't add abstractions until they solve an actual problem.

---

# 24. A Practical Laravel Architecture

A reasonable structure for a growing application might look like:

```text
app/
│
├── Http/
│   ├── Controllers/
│   └── Requests/
│
├── Models/
│
├── Services/
│
├── Contracts/
│
├── Repositories/
│
├── Actions/
│
└── Notifications/
```

But remember:

> Architecture should follow the needs of the application.

Don't create folders simply because a tutorial says they must exist.

---

# 25. Common Mistakes

### ❌ Mistake 1: Thinking SOLID means "more classes"

No.

The goal is:

```text
Better responsibility
Better boundaries
Lower coupling
Better maintainability
```

Not more files.

---

### ❌ Mistake 2: Creating interfaces everywhere

Don't do:

```php
UserServiceInterface
UserService
```

if there is only one implementation and no meaningful abstraction boundary.

---

### ❌ Mistake 3: Huge services

This:

```php
OrderService
```

shouldn't necessarily contain:

```text
Payment
Email
PDF
Inventory
Shipping
Tax
Discount
Authentication
```

Break responsibilities when they are genuinely distinct.

---

### ❌ Mistake 4: Confusing SRP with one method per class

This is unnecessary:

```php
CreateUserAction
UpdateUserAction
GetUserAction
DeleteUserAction
```

unless your application's complexity actually benefits from that separation.

---

# ⚡ Performance

SOLID can introduce additional objects and method calls.

But don't optimize these prematurely.

In Laravel applications, performance problems are much more likely to come from:

```text
N+1 queries
Slow database queries
Missing indexes
External API calls
Large payloads
Poor caching
```

than from having a few extra service objects.

---

# 🔐 Security

Good separation can help security.

For example:

```text
Request
  ↓
Form Request validation
  ↓
Controller
  ↓
Authorization
  ↓
Service
  ↓
Database
```

But SOLID itself isn't a security mechanism.

You still need:

* Authorization
* Validation
* Authentication
* CSRF protection
* Secure file handling
* Proper password hashing
* SQL injection protection

---

# 🎯 Practice Exercise

Build a notification system.

Start with:

```php
interface NotificationSender
{
    public function send(string $message): void;
}
```

Create:

```text
EmailNotificationSender
SmsNotificationSender
```

Then:

```php
final class NotificationService
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

Now:

```php
$service = new NotificationService(
    new EmailNotificationSender()
);

$service->notify('Your order has shipped.');
```

Then switch to SMS **without changing `NotificationService`**.

---

# 🔥 Challenge

Design this:

```text
                  ReportGenerator
                         │
                  ReportFormatter
                         │
            ┌────────────┼────────────┐
            ▼            ▼            ▼
           PDF          CSV          JSON
```

Create:

```php
interface ReportFormatter
{
    public function format(array $data): string;
}
```

Implement:

```text
PdfReportFormatter
CsvReportFormatter
JsonReportFormatter
```

Then:

```php
final class ReportGenerator
{
    public function __construct(
        private ReportFormatter $formatter,
    ) {
    }

    public function generate(array $data): string
    {
        return $this->formatter->format($data);
    }
}
```

Your `ReportGenerator` should **not know** whether the output is PDF, CSV, or JSON.

---

# 🧠 Interview Questions

Try answering these yourself:

1. What does SOLID stand for?
2. What is the Single Responsibility Principle?
3. Does SRP mean one method per class?
4. What is the Open/Closed Principle?
5. What is the Liskov Substitution Principle?
6. What is the Interface Segregation Principle?
7. What is Dependency Inversion?
8. What's the difference between dependency injection and dependency inversion?
9. Why should business logic depend on abstractions?
10. Should every Laravel class have an interface?
11. Why can overusing SOLID become a problem?
12. Give an example of violating SRP in a Laravel controller.

---

# 📌 Your SOLID Mental Model

Remember this:

```text
S → One focused responsibility
O → Extend without constantly modifying
L → Subtypes must honor the contract
I → Small, focused interfaces
D → Depend on abstractions
```

And the most important Laravel connection:

```text
              Controller
                   │
                   ▼
                Service
                   │
                   ▼
               Interface
                   ▲
                   │
        ┌──────────┼──────────┐
        │          │          │
     Stripe      PayPal      Bank
```

This is the foundation for the next major topic.

## 🚀 Next Lesson: Namespaces & Autoloading

We'll learn how PHP organizes classes across files:

```text
namespace
use
PSR-4
Composer autoloading
vendor/autoload.php
```

Then you'll understand **how Laravel can automatically find and load classes without manually writing `require` for every PHP file**.
