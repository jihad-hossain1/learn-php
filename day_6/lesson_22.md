# Lesson 22 — Functional Programming Patterns in PHP

Now we move from individual PHP features like **closures, generators, arrays, and callables** into a very useful professional skill:

> **How to process data cleanly without writing large, deeply nested loops.**

This is especially useful in Laravel because Laravel Collections are heavily based on these patterns.

---

## 1. Beginner Explanation

Functional programming is a style where we focus on:

> **Input → Transformation → Output**

Instead of telling PHP every tiny step, we describe **what should happen to the data**.

For example:

```php
$prices = [100, 200, 300];

$discountedPrices = array_map(
    fn (int $price): float => $price * 0.9,
    $prices
);
```

Result:

```text
[90, 180, 270]
```

Compare that with a traditional loop:

```php
$discountedPrices = [];

foreach ($prices as $price) {
    $discountedPrices[] = $price * 0.9;
}
```

Both are valid.

But functional patterns become very powerful when you combine multiple transformations.

---

# 2. Real-World Analogy

Imagine an e-commerce warehouse.

You have:

```text
100 products
     ↓
Remove inactive products
     ↓
Calculate discounted prices
     ↓
Calculate total
     ↓
Generate report
```

Instead of manually handling every product, you create a series of transformations:

```text
Products
   ↓
filter()
   ↓
map()
   ↓
reduce()
   ↓
Result
```

This is called a **pipeline**.

---

# 3. `map()` — Transform Every Item

Suppose:

```php
$prices = [100, 200, 300];
```

We want to add 10% tax.

```php
$pricesWithTax = array_map(
    fn (int $price): float => $price * 1.10,
    $prices
);
```

Result:

```php
[
    110,
    220,
    330,
]
```

### Important idea

`map()` means:

> "Take every item and transform it."

```text
100 → 110
200 → 220
300 → 330
```

---

# 4. `filter()` — Keep Certain Items

Suppose:

```php
$prices = [50, 100, 150, 200];
```

Only keep prices greater than 100:

```php
$expensiveProducts = array_filter(
    $prices,
    fn (int $price): bool => $price > 100
);
```

Result:

```php
[
    150,
    200,
]
```

Think:

```text
50   ❌
100  ❌
150  ✅
200  ✅
```

So:

> `filter()` removes items that don't satisfy a condition.

---

# 5. `reduce()` — Turn Many Items Into One

Suppose:

```php
$prices = [100, 200, 300];
```

We want the total.

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

Conceptually:

```text
0 + 100 = 100
100 + 200 = 300
300 + 300 = 600
```

So:

> `reduce()` transforms many values into a single value.

---

# 6. Combining Them

Now let's build something realistic.

```php
$products = [
    ['name' => 'Laptop', 'price' => 1000, 'active' => true],
    ['name' => 'Mouse', 'price' => 50, 'active' => false],
    ['name' => 'Keyboard', 'price' => 100, 'active' => true],
];
```

We want:

1. active products only
2. apply 10% discount
3. calculate total

### Step 1 — Filter

```php
$activeProducts = array_filter(
    $products,
    fn (array $product): bool => $product['active']
);
```

### Step 2 — Map

```php
$discountedProducts = array_map(
    function (array $product): array {
        $product['price'] *= 0.9;

        return $product;
    },
    $activeProducts
);
```

### Step 3 — Reduce

```php
$total = array_reduce(
    $discountedProducts,
    fn (float $total, array $product): float =>
        $total + $product['price'],
    0.0
);
```

Result:

```text
Laptop     900
Keyboard    90

Total      990
```

The overall pipeline is:

```text
products
   │
   ▼
filter()
   │
   ▼
active products
   │
   ▼
map()
   │
   ▼
discounted products
   │
   ▼
reduce()
   │
   ▼
total
```

---

# 7. Laravel Collections

This is where these concepts become extremely useful.

Laravel provides:

```php
collect()
```

Example:

```php
$prices = collect([100, 200, 300]);
```

Then:

```php
$total = $prices
    ->map(fn (int $price): float => $price * 1.10)
    ->sum();
```

Result:

```text
660
```

This is much easier to read.

---

# 8. Laravel Product Example

Suppose we have:

```php
$products = collect([
    ['name' => 'Laptop', 'price' => 1000, 'active' => true],
    ['name' => 'Mouse', 'price' => 50, 'active' => false],
    ['name' => 'Keyboard', 'price' => 100, 'active' => true],
]);
```

We can write:

```php
$total = $products
    ->filter(fn (array $product): bool => $product['active'])
    ->map(fn (array $product): float => $product['price'] * 0.9)
    ->sum();
```

Result:

```text
990
```

This is a **pipeline**.

---

# 9. Why Laravel Developers Love Collections

Without Collections:

```php
$activeProducts = [];

foreach ($products as $product) {
    if ($product['active']) {
        $activeProducts[] = $product;
    }
}
```

Then another loop:

```php
$discountedProducts = [];

foreach ($activeProducts as $product) {
    $discountedProducts[] = $product['price'] * 0.9;
}
```

Then another:

```php
$total = 0;

foreach ($discountedProducts as $price) {
    $total += $price;
}
```

With Collections:

```php
$total = $products
    ->filter(fn (array $product): bool => $product['active'])
    ->map(fn (array $product): float => $product['price'] * 0.9)
    ->sum();
```

The intent is much clearer.

---

# 10. Higher-Order Functions

A **higher-order function** is a function that:

- accepts another function as an argument, or
- returns a function.

For example:

```php
function applyOperation(
    int $value,
    callable $operation
): int {
    return $operation($value);
}
```

Then:

```php
$result = applyOperation(
    10,
    fn (int $value): int => $value * 2
);
```

Result:

```text
20
```

The function receives behavior as data.

---

# 11. Creating Reusable Pipelines

Imagine your application has many places where discounts are calculated.

Instead of:

```php
fn (float $price): float => $price * 0.9
```

everywhere, create a dedicated class.

```php
declare(strict_types=1);

final class DiscountCalculator
{
    public function apply(float $price): float
    {
        return $price * 0.9;
    }
}
```

Then:

```php
$calculator = new DiscountCalculator();

$prices = collect([100, 200, 300]);

$result = $prices
    ->map(
        fn (float $price): float => $calculator->apply($price)
    )
    ->sum();
```

This is preferable when the business rule becomes more complicated.

---

# 12. Immutability

A very important functional-programming concept is:

> **Don't unexpectedly modify existing data.**

Bad:

```php
$product['price'] = $product['price'] * 0.9;
```

You have modified the original array.

A safer approach:

```php
$discountedProduct = [
    ...$product,
    'price' => $product['price'] * 0.9,
];
```

The original remains unchanged.

This is especially useful when multiple parts of your application use the same data.

---

# 13. PHP Array vs Laravel Collection

| Feature             | PHP Array              | Laravel Collection |
| ------------------- | ---------------------- | ------------------ |
| `map`               | `array_map()`          | `map()`            |
| `filter`            | `array_filter()`       | `filter()`         |
| reduce              | `array_reduce()`       | `reduce()`         |
| sum                 | manual / `array_sum()` | `sum()`            |
| chaining            | limited                | excellent          |
| Laravel integration | basic                  | excellent          |
| Database results    | ❌                     | ✅                 |

---

# 14. Eloquent Example

Suppose:

```php
$orders = Order::query()
    ->where('status', 'completed')
    ->get();
```

Laravel returns a Collection.

You can do:

```php
$total = $orders
    ->filter(fn (Order $order): bool => $order->total > 100)
    ->sum('total');
```

Or:

```php
$customerNames = $orders
    ->map(fn (Order $order): string => $order->customer->name)
    ->unique()
    ->values();
```

This becomes extremely powerful for application-level data processing.

---

# 15. But Don't Overuse Collections

There is an important performance lesson.

Suppose you have **1 million database records**.

Don't do:

```php
$orders = Order::all();

$total = $orders
    ->filter(...)
    ->map(...)
    ->sum();
```

You just loaded potentially huge amounts of data into memory.

Instead, let the database do the work:

```php
$total = Order::query()
    ->where('status', 'completed')
    ->where('total', '>', 100)
    ->sum('total');
```

### Professional rule

> **Let the database handle database operations.**

Use Collections for data already loaded into your application.

Use SQL/query builder/Eloquent queries for filtering, aggregation, sorting, and operations that can efficiently happen in the database.

---

# 16. Performance

Consider:

```text
Database
   │
   │ 1,000,000 rows
   ▼
PHP Memory
   │
   ▼
Collection
```

This can be expensive.

Better:

```text
Database
   │
   │ WHERE + SUM
   ▼
One result
   │
   ▼
PHP
```

This reduces:

- memory usage
- network transfer
- PHP processing
- execution time

---

# 17. Security

Functional programming doesn't automatically make code secure.

You still need to validate input.

For example, don't trust:

```php
$discount = $_POST['discount'];
```

and blindly do:

```php
$price * $discount;
```

Validate it first.

In Laravel:

```php
$request->validate([
    'discount' => ['required', 'numeric', 'min:0', 'max:100'],
]);
```

Then your transformation logic can safely operate on validated data.

---

# 18. Common Mistakes

### Mistake 1 — Huge chained pipelines

This:

```php
$result
    ->filter(...)
    ->map(...)
    ->filter(...)
    ->map(...)
    ->filter(...)
    ->reduce(...)
    ->map(...)
    ->sort(...)
    ->...
```

can become difficult to understand.

Break complex business logic into methods/classes.

---

### Mistake 2 — Doing database work in PHP

Bad:

```php
Order::all()
    ->filter(...)
    ->sum(...);
```

Better:

```php
Order::query()
    ->where(...)
    ->sum(...);
```

---

### Mistake 3 — Mutating objects unexpectedly

Be careful with:

```php
->map(function ($product) {
    $product->price = 100;

    return $product;
});
```

You're modifying the object itself.

Understand whether you want mutation or a new transformed value.

---

### Mistake 4 — Using closures for everything

Closures are excellent for small transformations.

But this:

```php
->map(function ($order) {
    // 50 lines of business logic
});
```

is a code smell.

Move complex business logic into a class/service.

---

# 19. When Should You Use This?

Use functional patterns when:

- transforming collections
- filtering data
- calculating values
- processing API responses
- manipulating DTO data
- creating readable data pipelines
- working with Laravel Collections

Don't force functional patterns when:

- a simple `foreach` is clearer
- complex state is involved
- database operations should happen in SQL
- debugging becomes difficult
- business logic belongs in a service/domain class

---

# 20. Interview Questions

### Beginner

**Q: What does `array_map()` do?**

It transforms every element of an array.

---

**Q: What does `array_filter()` do?**

It keeps elements that satisfy a condition.

---

**Q: What does `array_reduce()` do?**

It reduces multiple values into a single result.

---

### Intermediate

**Q: What is a higher-order function?**

A function that accepts another function/callable or returns one.

---

**Q: What is a pipeline?**

A sequence of transformations where the output of one operation becomes the input of the next.

```text
input → filter → map → reduce → output
```

---

### Advanced

**Q: Why shouldn't you use Laravel Collections for millions of database records?**

Because the records may already be loaded into PHP memory. Database filtering and aggregation are generally more efficient when performed by the database.

---

# 21. Small Exercise 📝

Given:

```php
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
```

Using `array_filter()`, `array_map()`, and `array_reduce()`:

### Task 1

Keep only even numbers.

Expected:

```text
[2, 4, 6, 8, 10]
```

### Task 2

Multiply every remaining number by 10.

Expected:

```text
[20, 40, 60, 80, 100]
```

### Task 3

Calculate the total.

Expected:

```text
300
```

---

# 22. Challenge 🔥

Build a small **Order Price Pipeline**.

Input:

```php
$orders = [
    ['price' => 100, 'status' => 'completed'],
    ['price' => 200, 'status' => 'pending'],
    ['price' => 300, 'status' => 'completed'],
    ['price' => 400, 'status' => 'cancelled'],
];
```

Your pipeline should:

```text
Orders
   ↓
Keep completed orders
   ↓
Apply 10% discount
   ↓
Calculate total
```

Expected:

```text
360
```

Try both:

1. **Raw PHP**
2. **Laravel Collection**

---

# 23. What You Learned

Today you connected several PHP concepts:

```text
Closures
   ↓
Callbacks
   ↓
map()
   ↓
filter()
   ↓
reduce()
   ↓
Pipelines
   ↓
Laravel Collections
   ↓
Efficient Laravel data processing
```

The most important professional lesson is:

> **Use functional patterns to make transformations clear, but don't sacrifice readability or database performance just to avoid a `foreach`.**

### Next lesson → **PHP Design Patterns**

We'll start learning how professional PHP applications are structured using patterns such as:

- Factory
- Strategy
- Repository
- Adapter
- Decorator
- Observer
- Dependency Injection
- Service Layer

This is where your PHP knowledge starts moving toward **professional backend architecture**.
