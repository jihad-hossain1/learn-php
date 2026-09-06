# 🚀 PHP 8.3 — Lesson 18: Generators & `yield`

Today we'll learn **Generators**.

This is an important intermediate PHP concept because it helps you process **large amounts of data efficiently**.

---

# 1. The problem: large arrays

Imagine you have 10 million products.

A normal function might do:

```php
function getProducts(): array
{
    $products = [];

    for ($i = 1; $i <= 10_000_000; $i++) {
        $products[] = "Product {$i}";
    }

    return $products;
}
```

Then:

```php
$products = getProducts();

foreach ($products as $product) {
    echo $product;
}
```

The problem is:

```text
Generate 10 million products
        ↓
Store ALL in memory
        ↓
Return array
        ↓
Process them
```

That can consume a **lot of memory**.

Generators solve this problem.

---

# 2. What is a Generator?

A generator allows a function to produce values **one at a time** instead of creating the entire collection in memory.

The key keyword is:

```php
yield
```

Example:

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

Use it:

```php
foreach (numbers() as $number) {
    echo $number . PHP_EOL;
}
```

Output:

```text
1
2
3
```

---

# 3. `return` vs `yield`

This is the most important concept.

### Normal function

```php
function numbers(): array
{
    return [1, 2, 3];
}
```

The entire array is created first.

### Generator

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

Values are produced as needed.

Think:

```text
Array

Create:
[1, 2, 3]
 ↓
Return everything


Generator

Produce 1
 ↓
pause

Produce 2
 ↓
pause

Produce 3
 ↓
finish
```

---

# 4. Real-world analogy

Imagine a restaurant kitchen.

### Array approach

The chef prepares **10,000 meals** and puts all of them on tables before customers arrive.

That's wasteful.

### Generator approach

The chef prepares:

```text
Meal 1 → customer
Meal 2 → customer
Meal 3 → customer
...
```

Only what is needed is produced.

That's roughly how generators work.

---

# 5. A simple generator

```php
<?php

declare(strict_types=1);

function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}

foreach (numbers() as $number) {
    echo $number . PHP_EOL;
}
```

The return type:

```php
Generator
```

comes from PHP's built-in `Generator` class.

---

# 6. Generator with a loop

Generators become much more useful with loops.

```php
function numbers(int $maximum): Generator
{
    for ($i = 1; $i <= $maximum; $i++) {
        yield $i;
    }
}
```

Now:

```php
foreach (numbers(5) as $number) {
    echo $number . PHP_EOL;
}
```

Output:

```text
1
2
3
4
5
```

Notice we didn't create:

```php
[1, 2, 3, 4, 5]
```

Instead, each value is yielded when needed.

---

# 7. Why memory usage matters

Consider:

```php
function createArray(): array
{
    $numbers = [];

    for ($i = 1; $i <= 1_000_000; $i++) {
        $numbers[] = $i;
    }

    return $numbers;
}
```

This stores a million values.

A generator:

```php
function createGenerator(): Generator
{
    for ($i = 1; $i <= 1_000_000; $i++) {
        yield $i;
    }
}
```

doesn't need to keep all million values in the returned sequence.

Conceptually:

```text
Array
Memory
████████████████████████████

Generator
Memory
██
```

The exact memory usage depends on what your generator does, but the key idea is **lazy evaluation**.

---

# 8. What does "lazy" mean?

Lazy means:

> Don't calculate something until it is actually needed.

Example:

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

When you call:

```php
$numbers = numbers();
```

PHP doesn't immediately execute the whole function.

The generator starts producing values as you iterate over it.

---

# 9. Generator execution

Consider:

```php
function numbers(): Generator
{
    echo "Start\n";

    yield 1;

    echo "Middle\n";

    yield 2;

    echo "End\n";

    yield 3;
}
```

Then:

```php
foreach (numbers() as $number) {
    echo "Number: {$number}\n";
}
```

The execution roughly happens like:

```text
Start
Number: 1

Middle
Number: 2

End
Number: 3
```

The important concept is that `yield` **pauses** the generator.

When iteration asks for the next value, execution resumes from where it stopped.

---

# 10. `yield` vs `return`

Consider:

```php
function example(): Generator
{
    yield 1;
    yield 2;

    return 100;
}
```

The yielded values are:

```text
1
2
```

The `return 100` isn't another yielded value.

The generator can have a return value, which can be retrieved with:

```php
$generator->getReturn();
```

Example:

```php
$generator = example();

foreach ($generator as $value) {
    echo $value . PHP_EOL;
}

echo $generator->getReturn();
```

Output:

```text
1
2
100
```

This is less common in everyday Laravel development, but useful to understand.

---

# 11. Generators can yield keys

You can yield:

```php
yield $key => $value;
```

Example:

```php
function users(): Generator
{
    yield 1 => 'John';
    yield 2 => 'Sarah';
    yield 3 => 'Mike';
}
```

Then:

```php
foreach (users() as $id => $name) {
    echo "{$id}: {$name}" . PHP_EOL;
}
```

Output:

```text
1: John
2: Sarah
3: Mike
```

---

# 12. Real-world example: CSV processing

Imagine a CSV file containing:

```text
id,name,email
1,John,john@example.com
2,Sarah,sarah@example.com
3,Mike,mike@example.com
...
```

A bad approach for a huge file is:

```php
$rows = file('users.csv');
```

You could potentially load a huge amount of data into memory.

Instead, create a generator:

```php
function readUsers(string $filePath): Generator
{
    $handle = fopen($filePath, 'rb');

    if ($handle === false) {
        throw new RuntimeException('Unable to open file.');
    }

    try {
        while (($row = fgetcsv($handle)) !== false) {
            yield $row;
        }
    } finally {
        fclose($handle);
    }
}
```

Now:

```php
foreach (readUsers('users.csv') as $row) {
    // Process one row.
}
```

The flow becomes:

```text
CSV file
   ↓
read one row
   ↓
yield row
   ↓
process row
   ↓
read next row
   ↓
yield row
   ↓
...
```

This is extremely useful for large imports.

---

# 13. Generator + database processing

Suppose you need to process many records.

Instead of thinking:

```text
Load everything
     ↓
Process everything
```

you can design:

```text
Fetch chunk
    ↓
Process
    ↓
Fetch next chunk
    ↓
Process
```

Laravel provides database tools such as `chunk()` and `lazy()` that follow this general memory-efficient philosophy.

For example:

```php
User::query()
    ->lazy()
    ->each(function (User $user): void {
        // Process one user.
    });
```

The exact database behavior is handled by Laravel.

You don't need to manually build a generator for every large query.

---

# 14. Laravel `lazy()`

Suppose you have:

```php
$users = User::query()->lazy();
```

You can do:

```php
foreach ($users as $user) {
    // Process user.
}
```

This is useful when processing a large number of records.

Conceptually:

```text
Database
   ↓
small batch
   ↓
User 1
User 2
User 3
   ↓
next batch
   ↓
User 4
User 5
...
```

This is much better than blindly doing:

```php
$users = User::all();
```

when the table contains millions of records.

---

# 15. `cursor()`

Laravel also provides:

```php
User::query()->cursor();
```

which gives you a lazy iterable over query results.

Example:

```php
foreach (User::query()->cursor() as $user) {
    // Process one user.
}
```

For large datasets, this can significantly reduce application memory usage.

However, database cursors have trade-offs, and sometimes `lazy()`/chunking is preferable depending on the query and workload.

---

# 16. Generator vs Array

| Feature                | Array          | Generator                     |
| ---------------------- | -------------- | ----------------------------- |
| Stores all values      | Usually yes    | No                            |
| Lazy                   | ❌             | ✅                            |
| Memory efficient       | For small data | Excellent for large sequences |
| Random access          | ✅             | ❌                            |
| `count()` directly     | ✅             | Not like an array             |
| Reusable iteration     | Usually yes    | Generally one iteration       |
| Good for huge datasets | Sometimes      | ✅                            |

---

# 17. Important limitation

A generator isn't magic.

Suppose you write:

```php
function numbers(): Generator
{
    $numbers = [];

    for ($i = 1; $i <= 1_000_000; $i++) {
        $numbers[] = $i;
    }

    foreach ($numbers as $number) {
        yield $number;
    }
}
```

This still stores all million numbers.

So you haven't really solved the memory problem.

The useful pattern is:

```php
function numbers(): Generator
{
    for ($i = 1; $i <= 1_000_000; $i++) {
        yield $i;
    }
}
```

---

# 18. Generator + service architecture

In a Laravel application, you might have:

```text
Controller
    ↓
ImportService
    ↓
Generator
    ↓
CSV / Database
```

For example:

```php
final class UserImportService
{
    public function import(string $filePath): void
    {
        foreach ($this->readUsers($filePath) as $userData) {
            $this->createUser($userData);
        }
    }

    private function readUsers(string $filePath): Generator
    {
        $handle = fopen($filePath, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Unable to open file.');
        }

        try {
            while (($row = fgetcsv($handle)) !== false) {
                yield $row;
            }
        } finally {
            fclose($handle);
        }
    }

    private function createUser(array $userData): void
    {
        // Create user.
    }
}
```

The controller stays thin:

```php
final class UserImportController
{
    public function store(
        UserImportService $importService
    ): Response {
        $importService->import('/path/users.csv');

        return response('Import completed.');
    }
}
```

That's much closer to production architecture.

---

# 19. Generator lifecycle

Think of it like this:

```text
                 Generator
                    │
                    ▼
              Start execution
                    │
                    ▼
                 yield 1
                    │
                  PAUSE
                    │
                    ▼
             foreach requests
              next value
                    │
                    ▼
                 yield 2
                    │
                  PAUSE
                    │
                    ▼
                 yield 3
                    │
                    ▼
                  DONE
```

This is the key mental model.

---

# 20. Common mistakes

### ❌ Mistake 1: Using generators everywhere

Don't use:

```php
Generator
```

just because it's technically possible.

For:

```php
$users = [
    'John',
    'Sarah',
    'Mike',
];
```

an array is simpler and perfectly appropriate.

---

### ❌ Mistake 2: Converting it back to an array

You can do:

```php
iterator_to_array($generator);
```

But now you've materialized the values into an array.

That can defeat the memory benefit.

---

### ❌ Mistake 3: Forgetting external resources

If you're reading:

```text
files
database streams
network resources
```

make sure resources are properly closed.

That's why the earlier example used:

```php
finally {
    fclose($handle);
}
```

---

### ❌ Mistake 4: Assuming generator means faster

Generators primarily help with **memory usage and lazy processing**.

They don't automatically make every operation faster.

Sometimes a normal array is actually faster for a small dataset.

---

# 21. Performance Best Practices

Use generators when:

- processing large files
- processing streams
- handling large sequences
- transforming large datasets
- building pipelines
- memory usage matters

Avoid generators when:

- the dataset is tiny
- you need random access
- you need to iterate multiple times
- an array is simpler and clearer

---

# 22. Security Considerations

Generators themselves aren't a security feature.

But they can help when handling untrusted large input.

For example:

```text
Huge CSV upload
       ↓
Don't load entire file
       ↓
Process incrementally
       ↓
Lower memory pressure
```

Still enforce:

- upload size limits
- file type validation
- authentication
- authorization
- input validation
- database constraints
- timeouts

A generator does **not** protect you from malicious input by itself.

---

# 23. Interview Questions

### Q1. What is a generator?

A function that can produce values lazily using `yield`.

### Q2. What does `yield` do?

It produces a value and pauses execution until the generator is resumed.

### Q3. Why use generators?

Primarily to process large or streaming data without storing the entire sequence in memory.

### Q4. What is the difference between `return` and `yield`?

```php
return
```

ends the function.

```php
yield
```

produces a value and pauses the generator.

### Q5. Can a generator have a return value?

Yes:

```php
return $value;
```

and retrieve it with:

```php
$generator->getReturn();
```

after iteration completes.

### Q6. Does Laravel use generator-like lazy processing?

Yes. Laravel provides APIs such as:

```php
lazy()
cursor()
```

for memory-conscious processing of large query results.

---

# 🧪 Small Exercise

Write this function:

```php
function evenNumbers(int $maximum): Generator
{
    // Your code
}
```

It should produce:

```text
2
4
6
8
10
```

when called with:

```php
evenNumbers(10)
```

Use:

```php
foreach (evenNumbers(10) as $number) {
    echo $number . PHP_EOL;
}
```

### 🔥 Challenge

Create:

```php
function readLargeNumbers(array $numbers): Generator
```

that:

1. Receives an array.
2. Yields only numbers greater than `100`.
3. Does **not** create another filtered array.
4. Uses strict typing.
5. Returns `Generator`.

For example:

```php
$numbers = [10, 150, 50, 200, 75, 300];
```

Expected:

```text
150
200
300
```

---

# 🎯 Summary

The most important idea from today's lesson is:

```text
Array
→ Give me everything now.

Generator
→ Give me one value when I need it.
```

The syntax is simple:

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

And the real-world benefit is:

```text
Large data
    ↓
Generator
    ↓
One value at a time
    ↓
Lower memory usage
```

For Laravel development, remember these particularly:

```php
User::query()->lazy();
```

and:

```php
User::query()->cursor();
```

You now have an important tool for building **memory-efficient backend applications**.

**Next lesson: PHP Attributes** — we'll learn how modern PHP attaches metadata to classes, methods, properties, and parameters, and how Laravel uses attributes behind the scenes.
