# 🚀 Module 2 — Lesson 16: Exceptions & Error Handling in PHP

Today we're learning how professional PHP applications handle failures safely.

You'll learn:

```text
Exception
   ↓
throw
   ↓
try / catch
   ↓
finally
   ↓
Custom Exceptions
   ↓
Laravel Exception Handling
```

---

# 1. What Is an Exception?

An exception represents an **unexpected situation or failure** while your program is running.

For example:

```php
$user = findUser(100);

if ($user === null) {
    throw new RuntimeException('User not found.');
}
```

Instead of silently continuing, the program says:

> Something went wrong. Stop normal execution and handle this problem.

Think of it like:

```text
Normal execution
      ↓
Something goes wrong
      ↓
Exception thrown
      ↓
PHP looks for a handler
      ↓
catch
      ↓
Handle the problem
```

---

# 2. Simple Example

```php
<?php

declare(strict_types=1);

throw new RuntimeException('Something went wrong.');
```

Execution stops at `throw`.

You'll get an uncaught exception if nothing handles it.

---

# 3. `try` and `catch`

To handle an exception:

```php
try {
    throw new RuntimeException('Payment failed.');
} catch (RuntimeException $exception) {
    echo $exception->getMessage();
}
```

Output:

```text
Payment failed.
```

The structure is:

```text
try
 ↓
Run risky code
 ↓
Exception?
 ↓
catch
 ↓
Handle exception
```

---

# 4. Why Use Exceptions?

Compare these approaches.

### Without exception

```php
$result = processPayment();

if ($result === false) {
    echo 'Payment failed.';
}
```

This can work for simple situations.

But with complex operations, errors can become difficult to propagate:

```text
Controller
   ↓
Service
   ↓
Payment
   ↓
API
   ↓
Database
```

Exceptions allow an error to travel upward until an appropriate layer handles it.

---

# 5. `throw`

The keyword:

```php
throw
```

creates an exception.

Example:

```php
function divide(float $a, float $b): float
{
    if ($b === 0.0) {
        throw new InvalidArgumentException(
            'Cannot divide by zero.'
        );
    }

    return $a / $b;
}
```

Then:

```php
try {
    echo divide(10, 0);
} catch (InvalidArgumentException $exception) {
    echo $exception->getMessage();
}
```

Output:

```text
Cannot divide by zero.
```

---

# 6. Exception Objects

An exception is an object.

For example:

```php
$exception = new RuntimeException('Something failed.');
```

You can inspect it:

```php
echo $exception->getMessage();
```

Other useful methods include:

```php
$exception->getCode();
$exception->getFile();
$exception->getLine();
$exception->getTrace();
```

For debugging:

```php
$exception->getTraceAsString();
```

---

# 7. Exception Hierarchy

PHP exceptions have a hierarchy.

Conceptually:

```text
Throwable
├── Error
└── Exception
     ├── RuntimeException
     ├── LogicException
     ├── InvalidArgumentException
     └── ...
```

The important concept is that `Throwable` is the common interface for things that can be thrown.

Most application-level failures should use appropriate `Exception` classes.

---

# 8. `Exception` vs `Error`

This distinction is important.

### Exception

Usually represents something your application can reasonably handle.

Example:

```php
throw new RuntimeException('Payment provider unavailable.');
```

### Error

Usually represents a serious programming/runtime problem.

For example, certain PHP engine errors can be represented by classes extending `Error`.

You generally shouldn't use exceptions to hide programming bugs.

---

# 9. Catching Different Exception Types

You can have multiple catches:

```php
try {
    // risky operation
} catch (InvalidArgumentException $exception) {
    // Invalid input
} catch (RuntimeException $exception) {
    // Runtime failure
}
```

PHP checks the catches in order.

So specific exceptions should generally come before broader ones.

---

# 10. Catching `Throwable`

You can technically do:

```php
try {
    // ...
} catch (Throwable $throwable) {
    // ...
}
```

This catches both `Exception` and `Error` types.

But don't blindly catch everything.

For example:

```php
catch (Throwable $throwable) {
    // Ignore it
}
```

is dangerous.

You might accidentally hide serious programming errors.

---

# 11. `finally`

`finally` runs whether an exception occurs or not.

Example:

```php
try {
    echo 'Processing...' . PHP_EOL;

    throw new RuntimeException('Failed.');
} catch (RuntimeException $exception) {
    echo $exception->getMessage() . PHP_EOL;
} finally {
    echo 'Finished.' . PHP_EOL;
}
```

Output:

```text
Processing...
Failed.
Finished.
```

Think:

```text
try
 ↓
success/failure
 ↓
catch if needed
 ↓
finally ALWAYS
```

---

# 12. When Is `finally` Useful?

It's useful when something must happen regardless of success or failure.

For example:

```text
Open resource
    ↓
Try operation
    ↓
Handle failure
    ↓
Cleanup resource
```

Although modern PHP features and application abstractions often reduce the need for manual cleanup, the concept remains important.

---

# 13. Custom Exceptions

In professional applications, you'll often create your own exception types.

Suppose we have an order system.

Create:

```php
<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class InsufficientStockException extends RuntimeException
{
}
```

Now your service can say exactly what went wrong:

```php
if ($product->stock < $quantity) {
    throw new InsufficientStockException(
        'Not enough stock available.'
    );
}
```

That's much better than:

```php
throw new RuntimeException('Error.');
```

because the application can distinguish the error type.

---

# 14. Why Custom Exceptions Matter

Imagine:

```text
OrderService
    ↓
InsufficientStockException
```

The controller or Laravel exception handler can recognize:

```php
InsufficientStockException
```

and respond appropriately.

For example:

```text
HTTP 409 Conflict
```

instead of returning a generic server error.

---

# 15. Real-World Example

Let's build a small checkout service.

```php
<?php

declare(strict_types=1);

final class Product
{
    public function __construct(
        public readonly float $price,
        public int $stock,
    ) {
    }
}
```

Custom exception:

```php
<?php

declare(strict_types=1);

final class InsufficientStockException extends RuntimeException
{
}
```

Service:

```php
<?php

declare(strict_types=1);

final class CheckoutService
{
    public function purchase(
        Product $product,
        int $quantity,
    ): float {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        if ($quantity > $product->stock) {
            throw new InsufficientStockException(
                'Not enough stock.'
            );
        }

        $product->stock -= $quantity;

        return $product->price * $quantity;
    }
}
```

Now:

```php
$product = new Product(
    price: 100,
    stock: 5,
);

$checkout = new CheckoutService();

try {
    $total = $checkout->purchase($product, 10);

    echo "Total: {$total}";
} catch (InsufficientStockException $exception) {
    echo $exception->getMessage();
}
```

Output:

```text
Not enough stock.
```

---

# 16. Exceptions Should Be Handled at the Right Layer

This is extremely important.

Don't do this everywhere:

```php
try {
    // ...
} catch (Exception $exception) {
    echo $exception->getMessage();
}
```

Instead, ask:

> Which layer actually knows how to respond to this failure?

For example:

```text
Database
   ↓
throws exception
   ↓
Service
   ↓
throws domain exception
   ↓
Controller / Exception Handler
   ↓
HTTP response
```

---

# 17. Laravel Example

Imagine:

```php
final class CheckoutService
{
    public function __construct(
        private PaymentGateway $paymentGateway,
    ) {
    }

    public function checkout(float $amount): void
    {
        $success = $this->paymentGateway->charge($amount);

        if (! $success) {
            throw new PaymentFailedException(
                'Payment could not be completed.'
            );
        }
    }
}
```

Your controller shouldn't necessarily do all the payment logic.

Instead:

```php
final class CheckoutController
{
    public function __construct(
        private CheckoutService $checkoutService,
    ) {
    }

    public function store(): JsonResponse
    {
        $this->checkoutService->checkout(500);

        return response()->json([
            'message' => 'Payment successful.',
        ]);
    }
}
```

If payment fails, the exception can propagate to Laravel's exception handling system.

---

# 18. Laravel Request Flow

A simplified Laravel request lifecycle:

```text
HTTP Request
     ↓
Route
     ↓
Middleware
     ↓
Controller
     ↓
Service
     ↓
Exception
     ↓
Laravel Exception Handling
     ↓
HTTP Response
```

The important part is:

> You don't necessarily need `try/catch` in every controller.

Laravel has centralized exception handling.

---

# 19. Don't Catch Exceptions Just to Re-throw Them

Avoid unnecessary code like:

```php
try {
    $order = $this->orderService->create();
} catch (Exception $exception) {
    throw $exception;
}
```

This accomplishes almost nothing.

If you aren't adding useful behavior, let the exception propagate.

---

# 20. Exception Wrapping

Sometimes you want to translate a low-level exception into a meaningful application-level exception.

For example:

```php
try {
    $this->paymentGateway->charge($amount);
} catch (RuntimeException $exception) {
    throw new PaymentFailedException(
        'Payment provider failed.',
        previous: $exception,
    );
}
```

The `previous` exception preserves the original cause.

This gives you:

```text
PaymentFailedException
        ↓
previous
        ↓
Original RuntimeException
```

Very useful for debugging.

---

# 21. Never Expose Internal Errors to Users

Suppose the database throws:

```text
SQLSTATE[HY000]: ...
```

You generally don't want to return raw internal database details to the user.

Bad:

```json
{
  "error": "SQLSTATE[HY000]: General error..."
}
```

Better:

```json
{
  "message": "Something went wrong. Please try again."
}
```

Meanwhile, the detailed exception should be logged securely for developers.

---

# 🔐 Security

Exception handling has an important security implication.

### Don't expose:

- Database errors
- SQL queries
- File paths
- Stack traces
- API credentials
- Internal service details
- Secrets

to normal users.

In production, make sure debugging isn't exposing sensitive internals.

---

# 22. Logging

When something unexpected happens, you often want to log it.

For example:

```php
try {
    $this->paymentGateway->charge($amount);
} catch (RuntimeException $exception) {
    logger()->error('Payment failed.', [
        'exception' => $exception,
    ]);

    throw $exception;
}
```

Be careful about what contextual data you log.

Never blindly log:

```text
passwords
credit card details
access tokens
API secrets
```

---

# 23. Performance

Exceptions are designed for exceptional situations, not normal control flow.

Avoid:

```php
try {
    // expected normal operation
} catch (...) {
    // this happens frequently
}
```

For expected outcomes, use normal control flow when appropriate.

For example, if a product might not exist, you may have an application-specific strategy for representing that state rather than intentionally throwing and catching exceptions thousands of times per second.

Exceptions are for **exceptional conditions**, not ordinary branching.

---

# 24. Common Mistakes

### ❌ Mistake 1 — Empty catch

```php
catch (Exception $exception) {
}
```

This hides failures.

---

### ❌ Mistake 2 — Catching everything

```php
catch (Throwable $throwable) {
    return null;
}
```

Now serious errors can disappear silently.

---

### ❌ Mistake 3 — Showing exception messages directly

```php
return response()->json([
    'error' => $exception->getMessage(),
]);
```

Not every exception message is safe for users.

---

### ❌ Mistake 4 — Using exceptions for normal logic

Don't use exceptions as a replacement for every `if`.

---

### ❌ Mistake 5 — Losing the original exception

Avoid:

```php
catch (RuntimeException $exception) {
    throw new PaymentFailedException();
}
```

when the original cause is useful.

Prefer:

```php
catch (RuntimeException $exception) {
    throw new PaymentFailedException(
        'Payment failed.',
        previous: $exception,
    );
}
```

---

# 🎯 Practice Exercise

Create:

```php
final class BankAccount
{
    public function __construct(
        private float $balance,
    ) {
    }

    public function withdraw(float $amount): void
    {
        // Your implementation
    }
}
```

Create:

```php
InsufficientBalanceException
```

Requirements:

```text
withdraw(100)
      ↓
amount <= 0?
      ↓
InvalidArgumentException

amount > balance?
      ↓
InsufficientBalanceException

otherwise
      ↓
withdraw successfully
```

Then test it using:

```php
try {
    $account->withdraw(500);
} catch (InsufficientBalanceException $exception) {
    echo $exception->getMessage();
}
```

---

# 🔥 Challenge

Build an order system:

```text
OrderService
     │
     ├── Product not found
     │       ↓
     │   ProductNotFoundException
     │
     ├── Insufficient stock
     │       ↓
     │   InsufficientStockException
     │
     └── Payment failed
             ↓
        PaymentFailedException
```

Then handle the exceptions appropriately at the application boundary.

Try to keep:

```text
Controller
```

thin.

The service should contain the business rules.

---

# 🧠 Interview Questions

Try answering these without looking:

1. What is an exception?
2. What does `throw` do?
3. What is the purpose of `try`?
4. What does `catch` do?
5. What does `finally` do?
6. What's the difference between `Exception` and `Error`?
7. What is `Throwable`?
8. Why create custom exceptions?
9. Why shouldn't exceptions be used for normal control flow?
10. Why shouldn't you catch every exception?
11. What is exception wrapping?
12. Why is the `previous` exception useful?
13. Where should exceptions generally be handled in a Laravel application?
14. Why shouldn't raw exception messages be shown to users?

---

# 📌 Summary

The professional mental model is:

```text
Something goes wrong
       ↓
throw Exception
       ↓
Exception travels upward
       ↓
Appropriate layer handles it
       ↓
User gets safe response
       ↓
Developer gets useful logs
```

And remember:

```text
throw       → create/raise a failure
try         → execute risky code
catch       → handle a failure
finally     → execute cleanup/final work
Exception   → application-level failure
Custom      → meaningful domain/application failure
```

### Next Lesson 🚀

**PHP Enums**

We'll learn how to replace fragile strings like:

```php
$status = 'pending';
$status = 'completed';
$status = 'cancelled';
```

with strongly defined PHP enums:

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

Then we'll use enums in **real Laravel models, validation, database columns, and business logic**.
