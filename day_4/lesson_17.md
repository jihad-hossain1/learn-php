# 🚀 PHP 8.3 — Lesson 17: Enums

Enums are one of the most useful modern PHP features, especially when building **Laravel applications with statuses, roles, types, categories, and states**.

---

## 1. What is an Enum?

An **enum** defines a fixed set of allowed values.

For example, an order can have only:

```text
pending
processing
completed
cancelled
```

Without enums, developers often use strings:

```php
$status = 'pending';
```

The problem is that this is easy to mistype:

```php
$status = 'pendding'; // ❌ typo
```

An enum gives us a controlled set of values.

```php
enum OrderStatus
{
    case Pending;
    case Processing;
    case Completed;
    case Cancelled;
}
```

Now:

```php
$status = OrderStatus::Pending;
```

---

# 2. Real-world analogy

Think about a traffic light.

There are only three valid states:

```text
RED
YELLOW
GREEN
```

You shouldn't be able to have:

```text
PURPLE
BLUE
ORANGE
```

An enum represents exactly this idea:

```php
enum TrafficLight
{
    case Red;
    case Yellow;
    case Green;
}
```

---

# 3. Basic Enum

```php
<?php

declare(strict_types=1);

enum OrderStatus
{
    case Pending;
    case Processing;
    case Completed;
    case Cancelled;
}
```

Using it:

```php
$status = OrderStatus::Pending;

if ($status === OrderStatus::Pending) {
    echo 'Order is waiting.';
}
```

Notice this:

```php
OrderStatus::Pending
```

is not a string.

It's an **enum object/value** representing that specific case.

---

# 4. Enum vs String

Without enum:

```php
$status = 'pending';

if ($status === 'pending') {
    // ...
}
```

With enum:

```php
$status = OrderStatus::Pending;

if ($status === OrderStatus::Pending) {
    // ...
}
```

The enum version prevents arbitrary values from being passed around.

---

# 5. Backed Enums

This is especially important for Laravel.

Suppose your database contains:

```text
pending
processing
completed
cancelled
```

We can associate each enum case with a string value.

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

Now:

```php
$status = OrderStatus::Pending;

echo $status->value;
```

Output:

```text
pending
```

So:

```php
OrderStatus::Pending
```

is the enum case, while:

```php
OrderStatus::Pending->value
```

is:

```text
pending
```

### Important distinction

```php
OrderStatus::Pending
```

→ enum case

```php
OrderStatus::Pending->value
```

→ `'pending'`

---

# 6. Why Backed Enums are useful

Your application can work with:

```php
OrderStatus::Pending
```

while your database stores:

```text
pending
```

Architecture:

```text
PHP Application
       ↓
OrderStatus::Pending
       ↓
     Eloquent
       ↓
   "pending"
       ↓
   Database
```

This gives you type safety in PHP while keeping normal database values.

---

# 7. `from()`

You can convert a backed value into an enum.

```php
$status = OrderStatus::from('pending');
```

Now:

```php
$status === OrderStatus::Pending
```

is:

```text
true
```

But be careful.

If the value doesn't exist:

```php
$status = OrderStatus::from('unknown');
```

PHP throws:

```text
ValueError
```

So `from()` means:

> "I expect this value to be valid."

---

# 8. `tryFrom()`

If the value might be invalid, use:

```php
$status = OrderStatus::tryFrom('unknown');
```

Instead of throwing an exception, it returns:

```php
null
```

Example:

```php
$status = OrderStatus::tryFrom('unknown');

if ($status === null) {
    echo 'Invalid status.';
}
```

### Difference

| Method      | Invalid value       |
| ----------- | ------------------- |
| `from()`    | Throws `ValueError` |
| `tryFrom()` | Returns `null`      |

This distinction is important in real applications.

---

# 9. Get all enum cases

Use:

```php
OrderStatus::cases();
```

Example:

```php
foreach (OrderStatus::cases() as $status) {
    echo $status->value . PHP_EOL;
}
```

Output:

```text
pending
processing
completed
cancelled
```

Useful for:

- dropdowns
- filters
- validation
- API responses
- admin panels

---

# 10. Enums can have methods

This is where enums become powerful.

```php
enum OrderStatus: string
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
            self::Cancelled => 'Order cancelled',
        };
    }
}
```

Usage:

```php
$status = OrderStatus::Processing;

echo $status->label();
```

Output:

```text
Being prepared
```

This is much cleaner than having status logic scattered throughout your application.

---

# 11. Enums + `match`

Enums work extremely well with `match`.

```php
$message = match ($status) {
    OrderStatus::Pending => 'Waiting for payment',
    OrderStatus::Processing => 'Preparing order',
    OrderStatus::Completed => 'Order delivered',
    OrderStatus::Cancelled => 'Order cancelled',
};
```

Because `match` is exhaustive, PHP can help you catch missing cases.

---

# 12. Enum static methods

Enums can also have static methods.

```php
enum OrderStatus: string
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
```

Usage:

```php
$status = OrderStatus::default();
```

---

# 13. Int-backed enums

Enums don't have to use strings.

```php
enum UserRole: int
{
    case Customer = 1;
    case Admin = 2;
    case Manager = 3;
}
```

Then:

```php
echo UserRole::Admin->value;
```

Output:

```text
2
```

But for application statuses, string-backed enums are usually easier to read:

```php
OrderStatus::Completed
```

with:

```text
completed
```

---

# 14. Laravel — Enum Casting

Now let's see where enums become really useful.

Suppose we have an `Order` model.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}
```

Now suppose the database contains:

```text
status = "pending"
```

When Laravel retrieves the model:

```php
$order->status
```

you get:

```php
OrderStatus::Pending
```

instead of:

```php
'pending'
```

That's a major advantage.

---

# 15. Using the Laravel Enum

Instead of:

```php
if ($order->status === 'completed') {
    // ...
}
```

you can write:

```php
if ($order->status === OrderStatus::Completed) {
    // ...
}
```

This is safer and easier to refactor.

---

# 16. Updating the status

You can assign the enum directly:

```php
$order->status = OrderStatus::Completed;

$order->save();
```

Laravel handles the conversion to the database value.

Conceptually:

```text
OrderStatus::Completed
        ↓
   Eloquent cast
        ↓
    "completed"
        ↓
     database
```

---

# 17. Laravel Validation

Suppose an API receives:

```json
{
  "status": "completed"
}
```

You can validate that the value belongs to the enum.

```php
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

$request->validate([
    'status' => [
        'required',
        Rule::enum(OrderStatus::class),
    ],
]);
```

Now:

```text
pending       ✅
processing    ✅
completed     ✅
cancelled     ✅
something     ❌
```

This is much better than manually maintaining:

```php
'in:pending,processing,completed,cancelled'
```

because the enum becomes the **single source of truth**.

---

# 18. Enum in a Service Class

Remember our architecture principle:

> Controllers should be thin.

Instead of putting business logic in the controller:

```php
if ($order->status === 'pending') {
    // complicated logic
}
```

we can use a service:

```php
final class OrderService
{
    public function complete(Order $order): void
    {
        if ($order->status !== OrderStatus::Processing) {
            throw new \DomainException(
                'Only processing orders can be completed.'
            );
        }

        $order->status = OrderStatus::Completed;
        $order->save();
    }
}
```

The enum communicates the domain clearly:

```php
OrderStatus::Processing
OrderStatus::Completed
```

---

# 19. Enum + Business Rules

You can put simple status-related behavior inside the enum.

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function canBeCancelled(): bool
    {
        return match ($this) {
            self::Pending,
            self::Processing => true,

            self::Completed,
            self::Cancelled => false,
        };
    }
}
```

Usage:

```php
if ($order->status->canBeCancelled()) {
    $order->status = OrderStatus::Cancelled;
}
```

This reads almost like English.

---

# 20. Database Migration

Your migration might look like:

```php
$table->string('status')->default('pending');
```

You generally **do not store the PHP enum object itself** in the database.

Instead:

```text
PHP:
OrderStatus::Pending

Database:
"pending"
```

The enum provides type safety at the application layer.

---

# 21. Very Important Mistake

Don't confuse:

```php
OrderStatus::Pending
```

with:

```php
'pending'
```

This is wrong:

```php
if ($order->status === 'pending') {
    // ...
}
```

if `$order->status` has been cast to the enum.

Use:

```php
if ($order->status === OrderStatus::Pending) {
    // ...
}
```

---

# 22. Another Common Mistake — Changing Values

Imagine your production database contains:

```text
pending
processing
completed
```

Then you change:

```php
case Completed = 'done';
```

Existing database records containing:

```text
completed
```

no longer map correctly.

So **backed enum values are part of your persisted data contract**.

Changing them requires a proper database/data migration.

---

# 23. When NOT to use enums

Enums are excellent when values are **fixed**.

Good:

```text
OrderStatus
UserRole
PaymentStatus
SubscriptionPlanType
InvoiceStatus
```

Not ideal:

```text
ProductCategory
```

if administrators can dynamically create categories.

For example:

```text
Electronics
Books
Clothing
Shoes
```

If these can change through an admin panel, use a database table instead.

### Rule

```text
Fixed by the application?
        ↓
      Enum

Dynamic/user-defined?
        ↓
    Database table
```

---

# 24. Enum Architecture

A production Laravel application might look like:

```text
app/
├── Enums/
│   ├── OrderStatus.php
│   ├── UserRole.php
│   └── PaymentStatus.php
│
├── Models/
│   └── Order.php
│
├── Services/
│   └── OrderService.php
│
└── Http/
    └── Requests/
        └── UpdateOrderStatusRequest.php
```

This keeps domain concepts organized.

---

# 25. Raw PHP Complete Example

```php
<?php

declare(strict_types=1);

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canBeCancelled(): bool
    {
        return match ($this) {
            self::Pending,
            self::Processing => true,

            self::Completed,
            self::Cancelled => false,
        };
    }
}

$status = OrderStatus::Processing;

echo $status->value . PHP_EOL;
echo $status->label() . PHP_EOL;

if ($status->canBeCancelled()) {
    echo 'Order can be cancelled.';
}
```

---

# 26. Performance

Enums are generally not something you need to optimize prematurely.

The bigger performance concerns are usually:

- database queries
- N+1 queries
- missing indexes
- unnecessary API calls
- expensive loops
- serialization
- caching

Don't replace enums with strings just because you're worried about performance.

Use enums when they make the domain safer and clearer.

---

# 27. Security

Enums can help reduce invalid application states.

For example, instead of trusting:

```php
$status = $request->input('status');
```

and assuming it's valid:

```php
$status = OrderStatus::tryFrom(
    $request->input('status')
);
```

Then validate the result.

However, enums **do not replace authorization**.

This is dangerous:

```php
$order->status = OrderStatus::Completed;
```

just because the value is valid.

You still need to determine:

```text
Is this status valid?
        +
Is this user allowed to perform this transition?
```

Validation and authorization are different problems.

---

# 28. Interview Questions

### Q1. What is an enum?

A type that defines a fixed set of named cases.

### Q2. What is a backed enum?

An enum whose cases have scalar values such as `string` or `int`.

```php
enum Status: string
{
    case Active = 'active';
}
```

### Q3. Difference between `from()` and `tryFrom()`?

```php
from()
```

throws `ValueError` for invalid values.

```php
tryFrom()
```

returns `null`.

### Q4. How do you get the backed value?

```php
$status->value;
```

### Q5. How do you get all cases?

```php
Status::cases();
```

### Q6. Why use enums in Laravel?

They provide:

- type safety
- readable business logic
- centralized allowed values
- easier validation
- Eloquent casting
- fewer magic strings

---

# 🧠 Small Exercise

Create:

```php
enum PaymentStatus: string
```

with these cases:

```text
Pending
Paid
Failed
Refunded
```

Then add:

```php
public function label(): string
```

and:

```php
public function isSuccessful(): bool
```

Expected behavior:

```php
PaymentStatus::Paid->isSuccessful();      // true
PaymentStatus::Pending->isSuccessful();   // false
PaymentStatus::Failed->isSuccessful();    // false
PaymentStatus::Refunded->isSuccessful();  // false
```

### Challenge

Then write:

```php
PaymentStatus::tryFrom('paid')
```

and determine what happens for:

```php
'paid'
'failed'
'unknown'
```

---

# 🎯 Summary

Remember these important pieces:

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
}
```

```php
OrderStatus::Pending
```

→ enum case

```php
OrderStatus::Pending->value
```

→ `'pending'`

```php
OrderStatus::from('pending')
```

→ enum or exception

```php
OrderStatus::tryFrom('pending')
```

→ enum or `null`

```php
OrderStatus::cases()
```

→ all cases

And in Laravel:

```php
protected function casts(): array
{
    return [
        'status' => OrderStatus::class,
    ];
}
```

This is one of the modern Laravel patterns you should become very comfortable with.

**Next lesson: PHP Generators (`yield`)** — we'll learn how PHP can process huge datasets efficiently without loading everything into memory at once.
