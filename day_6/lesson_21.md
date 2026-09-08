# 🚀 PHP 8.3 — Lesson 21: Closures & Callables

Today we'll learn **Closures, anonymous functions, arrow functions, callbacks, `use`, and first-class callables**.

This topic is extremely important for Laravel because you'll see it everywhere:

```php
Route::get(..., function () {
});

collect($users)->filter(function (...) {
});

->map(fn (...) => ...)

Event::listen(...);

Queue::dispatch(...);
```

If you understand closures properly, Laravel code becomes much easier to read.

---

# 1. What is a Closure?

A closure is an **anonymous function** that can be stored in a variable or passed to another function.

Normal function:

```php
function add(int $a, int $b): int
{
    return $a + $b;
}
```

Anonymous function:

```php
$add = function (int $a, int $b): int {
    return $a + $b;
};
```

Then:

```php
echo $add(10, 20);
```

Output:

```text
30
```

The function doesn't have a name.

That's why we call it an **anonymous function**.

---

# 2. Real-world analogy

Think of a function as a named tool:

```text
Calculator
```

You can call it whenever you want.

A closure is more like:

```text
"Here's a small tool. Use it right here."
```

For example:

```php
$numbers = [1, 2, 3, 4, 5];

$evenNumbers = array_filter(
    $numbers,
    function (int $number): bool {
        return $number % 2 === 0;
    }
);
```

The closure is a temporary piece of behavior passed to `array_filter()`.

---

# 3. Why are Closures important?

Because PHP functions can accept other functions.

This lets us build reusable behavior:

```text
Data
 ↓
Function
 ↓
Transformation
 ↓
Result
```

For example:

```php
$numbers = [1, 2, 3, 4, 5];

$result = array_map(
    function (int $number): int {
        return $number * 2;
    },
    $numbers
);
```

Result:

```php
[2, 4, 6, 8, 10]
```

---

# 4. Closure syntax

Basic syntax:

```php
$function = function (): void {
    echo 'Hello';
};
```

With parameters:

```php
$greet = function (string $name): string {
    return "Hello, {$name}";
};
```

Call it:

```php
echo $greet('John');
```

Output:

```text
Hello, John
```

---

# 5. Closures are objects

A closure isn't just some special piece of syntax.

PHP represents it using:

```php
Closure
```

For example:

```php
$greet = function (string $name): string {
    return "Hello {$name}";
};

var_dump($greet instanceof Closure);
```

Output:

```text
bool(true)
```

This becomes important when we discuss **callables**.

---

# 6. Passing a Closure to another function

This is one of the most important use cases.

```php
function processNumber(
    int $number,
    Closure $operation
): int {
    return $operation($number);
}
```

Then:

```php
$result = processNumber(
    10,
    function (int $number): int {
        return $number * 2;
    }
);

echo $result;
```

Output:

```text
20
```

Architecture:

```text
processNumber()
       ↑
       │
    behavior
       │
    Closure
```

The function doesn't need to know exactly what operation will happen.

---

# 7. Callbacks

A **callback** is something that can be called by another piece of code.

Example:

```php
$double = function (int $number): int {
    return $number * 2;
};
```

Pass it to:

```php
function apply(
    int $number,
    callable $callback
): int {
    return $callback($number);
}
```

Then:

```php
echo apply(10, $double);
```

Output:

```text
20
```

Notice the type:

```php
callable
```

rather than:

```php
Closure
```

---

# 8. `Closure` vs `callable`

This distinction matters.

### `Closure`

Specifically represents an anonymous function object.

```php
function execute(Closure $callback): mixed
{
    return $callback();
}
```

### `callable`

A broader type.

It can represent:

- Closure
- function name
- static method
- object method
- invokable object

So:

```php
callable
```

means:

> Anything PHP can call.

---

# 9. Example of different Callables

### Closure

```php
$callback = function (): string {
    return 'Hello';
};
```

### Named function

```php
function hello(): string
{
    return 'Hello';
}

$callback = 'hello';
```

### Static method

```php
$callback = [UserService::class, 'create'];
```

### Object method

```php
$service = new UserService();

$callback = [$service, 'create'];
```

All can be callable.

---

# 10. Arrow Functions

PHP also provides a shorter syntax:

```php
fn
```

Instead of:

```php
function (int $number): int {
    return $number * 2;
}
```

you can write:

```php
fn (int $number): int => $number * 2
```

Example:

```php
$double = fn (int $number): int => $number * 2;

echo $double(10);
```

Output:

```text
20
```

---

# 11. Arrow functions are great for simple logic

This:

```php
$numbers = array_map(
    function (int $number): int {
        return $number * 2;
    },
    $numbers
);
```

can become:

```php
$numbers = array_map(
    fn (int $number): int => $number * 2,
    $numbers
);
```

Much cleaner.

---

# 12. Multi-line logic

Don't force an arrow function when the logic is complicated.

Good:

```php
$double = fn (int $number): int => $number * 2;
```

For complex logic:

```php
$process = function (Order $order): void {
    if ($order->status !== OrderStatus::Pending) {
        return;
    }

    // More business logic...
};
```

Use the syntax that makes the code easiest to understand.

---

# 13. Capturing Variables with `use`

This is one of the most important Closure concepts.

Consider:

```php
$tax = 0.15;

$calculate = function (float $price) use ($tax): float {
    return $price + ($price * $tax);
};
```

Then:

```php
echo $calculate(100);
```

Output:

```text
115
```

The closure captures `$tax`.

---

# 14. Why `use` is needed

This won't work as you might initially expect:

```php
$tax = 0.15;

$calculate = function (float $price): float {
    return $price + ($price * $tax);
};
```

The closure does not automatically capture ordinary outer local variables.

You explicitly capture it:

```php
use ($tax)
```

So:

```text
Outer scope
    │
    └── $tax
          ↓
       use ($tax)
          ↓
      Closure
```

---

# 15. Capturing by value

By default:

```php
use ($tax)
```

captures the value.

Example:

```php
$tax = 0.10;

$calculate = function (float $price) use ($tax): float {
    return $price + ($price * $tax);
};

$tax = 0.20;

echo $calculate(100);
```

The closure still uses:

```text
10%
```

because it captured the original value.

---

# 16. Capturing by reference

You can use:

```php
use (&$tax)
```

The `&` means reference.

```php
$tax = 0.10;

$calculate = function () use (&$tax): float {
    return $tax;
};

$tax = 0.20;

echo $calculate();
```

Output:

```text
0.2
```

Now both refer to the same variable.

### Be careful

References can make code harder to reason about.

Use them only when you actually need shared mutable state.

---

# 17. Arrow functions capture automatically

This is an important difference.

With a normal closure:

```php
$tax = 0.15;

$calculate = function (float $price) use ($tax): float {
    return $price * $tax;
};
```

With an arrow function:

```php
$tax = 0.15;

$calculate = fn (float $price): float => $price * $tax;
```

Arrow functions automatically capture variables from the surrounding scope **by value**.

That's one reason they are convenient.

---

# 18. Example with `array_filter()`

Suppose:

```php
$prices = [50, 150, 200, 75, 300];
```

Find prices greater than 100:

```php
$result = array_filter(
    $prices,
    fn (int $price): bool => $price > 100
);
```

Result:

```text
150
200
300
```

This pattern appears constantly in Laravel Collections.

---

# 19. Example with `array_map()`

```php
$prices = [100, 200, 300];

$result = array_map(
    fn (int $price): int => $price * 2,
    $prices
);
```

Result:

```text
[200, 400, 600]
```

---

# 20. Example with `array_reduce()`

Suppose:

```php
$prices = [100, 200, 300];
```

Calculate total:

```php
$total = array_reduce(
    $prices,
    fn (int $carry, int $price): int => $carry + $price,
    0
);
```

Result:

```text
600
```

This is the functional programming style you'll encounter frequently in modern PHP.

---

# 21. Laravel Collections

Now the Laravel connection.

Laravel:

```php
collect()
```

gives you a Collection.

Example:

```php
$prices = collect([100, 200, 300]);
```

Map:

```php
$doublePrices = $prices->map(
    fn (int $price): int => $price * 2
);
```

Filter:

```php
$expensive = $prices->filter(
    fn (int $price): bool => $price > 100
);
```

This is why understanding closures is so important for Laravel.

---

# 22. Laravel `where` vs `filter`

Sometimes Laravel already provides a method for the operation.

Instead of:

```php
$users->filter(
    fn (User $user): bool => $user->active === true
);
```

you might use an appropriate built-in collection method when available.

The principle:

> Don't write a closure when the framework already provides a clearer operation.

Closures are powerful, but framework APIs often make common operations more readable.

---

# 23. Laravel Routes

Closures are also used in routing.

```php
Route::get('/hello', function () {
    return 'Hello';
});
```

The closure is the code Laravel executes when the route matches.

Conceptually:

```text
HTTP Request
     ↓
Router
     ↓
Route matches
     ↓
Closure executes
     ↓
Response
```

For small routes this can be fine.

For production business logic, don't put a giant closure in the route.

Prefer:

```php
Route::get(
    '/orders',
    [OrderController::class, 'index']
);
```

---

# 24. Laravel Dependency Injection + Closures

You can also use closures in service container configuration.

Conceptually:

```php
$this->app->bind(
    PaymentGateway::class,
    function ($app) {
        return new StripePaymentGateway(
            $app->make(HttpClient::class)
        );
    }
);
```

The closure tells the container:

> When someone requests this dependency, here's how to construct it.

This connects directly to the **Dependency Injection + Service Container** lessons we've already covered.

---

# 25. First-Class Callables

Modern PHP provides a very useful syntax:

```php
function add(int $a, int $b): int
{
    return $a + $b;
}

$operation = add(...);
```

Now `$operation` is a callable.

You can do:

```php
echo $operation(10, 20);
```

Output:

```text
30
```

This is called a **first-class callable**.

---

# 26. Why first-class Callables are useful

Before:

```php
$callback = 'strlen';
```

or:

```php
$callback = [$service, 'process'];
```

Modern PHP lets you write:

```php
$callback = $service->process(...);
```

or:

```php
$callback = SomeClass::process(...);
```

This provides a cleaner way to create callable references.

---

# 27. First-Class Callable Example

```php
final class PriceCalculator
{
    public function double(int $price): int
    {
        return $price * 2;
    }
}
```

Create the object:

```php
$calculator = new PriceCalculator();
```

Create callable:

```php
$double = $calculator->double(...);
```

Then:

```php
echo $double(100);
```

Output:

```text
200
```

---

# 28. Passing First-Class Callable to `array_map`

```php
$calculator = new PriceCalculator();

$prices = [100, 200, 300];

$result = array_map(
    $calculator->double(...),
    $prices
);
```

Result:

```text
[200, 400, 600]
```

This can be cleaner than writing:

```php
array_map(
    fn (int $price): int => $calculator->double($price),
    $prices
);
```

when you already have a suitable method.

---

# 29. Invokable Objects

PHP also allows objects to be called like functions if they implement:

```php
__invoke()
```

Example:

```php
final class CalculateTax
{
    public function __invoke(float $price): float
    {
        return $price * 0.15;
    }
}
```

Now:

```php
$calculateTax = new CalculateTax();

echo $calculateTax(100);
```

Output:

```text
15
```

This object is callable.

---

# 30. Why Invokable Objects are useful

A closure is great for small behavior:

```php
fn (int $price): int => $price * 2
```

But if the logic becomes substantial:

```text
Tax calculation
Discount calculation
Complex filtering
Authorization rule
Data transformation
```

an invokable class can be cleaner:

```php
final class CalculateTax
{
    public function __invoke(float $price): float
    {
        // Complex logic...
    }
}
```

Now you have:

```text
Simple behavior
    ↓
Closure

Complex reusable behavior
    ↓
Class
```

---

# 31. Production Architecture

Suppose you have:

```php
$orders->filter(
    fn (Order $order): bool =>
        $order->status === OrderStatus::Completed
);
```

This is perfectly reasonable for simple logic.

But if you have:

```text
20 conditions
database lookups
business rules
external API calls
authorization logic
```

don't create a massive closure.

Move it into a service/specification/query object.

For example:

```php
final class CompletedOrderFilter
{
    public function __invoke(Order $order): bool
    {
        return $order->status === OrderStatus::Completed;
    }
}
```

Then:

```php
$orders->filter(new CompletedOrderFilter());
```

This improves:

- testability
- readability
- reusability
- separation of concerns

---

# 32. Performance

Closures are generally efficient enough for normal application code.

Don't obsess over:

```php
function () {}
```

versus:

```php
fn () => ...
```

The bigger performance issues are usually:

```text
Database queries
N+1 queries
Large datasets
External APIs
Expensive algorithms
Repeated computation
```

However, avoid unnecessarily creating complex closures inside huge hot loops when a simpler approach exists.

---

# 33. Security

Closures aren't inherently dangerous.

But dynamically executing arbitrary callables can be dangerous if the callable comes from untrusted input.

Never do something like:

```php
$function = $request->input('function');

call_user_func($function);
```

without strict controls.

Never let users decide arbitrary PHP methods/functions to execute.

Instead use an allowlist:

```php
$allowedActions = [
    'activate',
    'deactivate',
];
```

Then explicitly map those actions to safe application behavior.

---

# 34. Common Mistakes

### ❌ Mistake 1: Huge closures

Bad:

```php
Route::get('/orders', function () {
    // 200 lines...
});
```

Move business logic into a service/controller.

---

### ❌ Mistake 2: Capturing too many variables

Bad:

```php
function (...) use (
    $user,
    $order,
    $database,
    $logger,
    $config,
    $somethingElse
) {
    // ...
}
```

This often indicates the closure is doing too much.

---

### ❌ Mistake 3: Using references unnecessarily

Avoid:

```php
use (&$variable)
```

unless shared mutation is genuinely required.

---

### ❌ Mistake 4: Using closures for reusable domain logic

If you need the same behavior in five places, consider a class or named method.

---

# 35. Interview Questions

### Q1. What is a Closure?

An anonymous function that can be stored, passed around, and invoked.

### Q2. What is a callback?

A callable passed to another function so that it can invoke it.

### Q3. `Closure` vs `callable`?

`Closure` represents a closure object.

`callable` is a broader type representing anything callable.

### Q4. Why do closures use `use`?

To capture variables from the surrounding scope.

```php
function () use ($tax) {
}
```

### Q5. How do arrow functions capture variables?

Automatically by value.

### Q6. What is a first-class callable?

A callable reference created using:

```php
$object->method(...);
ClassName::method(...);
functionName(...);
```

### Q7. What is `__invoke()`?

It allows an object to be called like a function.

---

# 🧪 Small Exercise

Create:

```php
$numbers = [10, 20, 30, 40, 50];
```

Using `array_map()` and an arrow function, transform it into:

```text
20
40
60
80
100
```

Then use `array_filter()` to keep only values greater than `50`.

---

# 🔥 Challenge

Build this:

```php
final class DiscountCalculator
{
    public function __invoke(float $price): float
    {
        // Apply 10% discount.
    }
}
```

Then:

```php
$prices = [100, 200, 300];

$calculator = new DiscountCalculator();

$discounted = array_map(
    $calculator,
    $prices
);
```

Expected:

```text
90
180
270
```

You're combining:

```text
Class
 ↓
__invoke()
 ↓
Callable object
 ↓
array_map()
```

This is a very useful pattern to recognize in professional PHP code.

---

# 🎯 Summary

The most important concepts today:

```text
Closure
   ↓
Anonymous function

Callable
   ↓
Anything PHP can call

Arrow function
   ↓
Short closure syntax

use
   ↓
Capture outer variables

First-class callable
   ↓
Create callable reference cleanly

__invoke()
   ↓
Make an object callable
```

And in Laravel:

```text
Route closures
       ↓
Collection callbacks
       ↓
Service container closures
       ↓
Events / queues / callbacks
       ↓
Application behavior
```

You should now be comfortable reading code like:

```php
$users
    ->filter(
        fn (User $user): bool => $user->isActive()
    )
    ->map(
        fn (User $user): string => $user->name
    );
```

This is a very common style in modern Laravel.

**Next lesson: PHP Functional Programming Patterns** — we'll combine closures, `map`, `filter`, `reduce`, higher-order functions, immutability, and pipelines, then apply them to realistic Laravel data-processing problems.

I can also give you a **3-question practice quiz** on closures and callables here before we move to the next lesson.
