# 🚀 PHP 8.3 — Lesson 20: Iterators & SPL

Today we're going one level deeper into PHP's iteration system.

You already learned **Generators**. Now we'll understand what is happening underneath `foreach`, and how PHP's **Iterator**, **IteratorAggregate**, and **SPL** classes let us create custom iterable objects.

---

# 1. Why do we need Iterators?

You've probably written:

```php
$users = ['John', 'Sarah', 'Mike'];

foreach ($users as $user) {
    echo $user;
}
```

`foreach` can iterate over arrays easily.

But what if we have an object?

```php
final class UserCollection
{
    private array $users = [];

    // ...
}
```

Can we do this?

```php
$users = new UserCollection();

foreach ($users as $user) {
    // ...
}
```

Not automatically in the way we want.

This is where **Iterators** come in.

---

# 2. Real-world analogy

Imagine a bookshelf:

```text
┌─────────┐
│ Book 1  │ ← current
│ Book 2  │
│ Book 3  │
│ Book 4  │
└─────────┘
```

An iterator is like a person pointing at the current book.

It knows:

```text
Where am I?
Is there another book?
Give me the current book.
Move to the next book.
```

In PHP:

```text
current()
key()
next()
rewind()
valid()
```

These methods form the basic `Iterator` contract.

---

# 3. The Iterator Interface

PHP provides:

```php
Iterator
```

A class implementing it needs these methods:

```php
interface Iterator extends Traversable
{
    public function current(): mixed;

    public function key(): mixed;

    public function next(): void;

    public function rewind(): void;

    public function valid(): bool;
}
```

You don't normally write this interface yourself; PHP provides it.

---

# 4. Building our first Iterator

Let's create a collection of products.

```php
<?php

declare(strict_types=1);

final class ProductIterator implements Iterator
{
    private int $position = 0;

    public function __construct(
        private array $products,
    ) {
    }

    public function current(): mixed
    {
        return $this->products[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->products[$this->position]);
    }
}
```

Now:

```php
$products = new ProductIterator([
    'Laptop',
    'Keyboard',
    'Mouse',
]);
```

We can use:

```php
foreach ($products as $product) {
    echo $product . PHP_EOL;
}
```

Output:

```text
Laptop
Keyboard
Mouse
```

---

# 5. What does `foreach` actually do?

This is extremely important.

When you write:

```php
foreach ($products as $product) {
    echo $product;
}
```

conceptually PHP does something like:

```text
rewind()
   ↓
valid()
   ↓
current()
   ↓
your code
   ↓
next()
   ↓
valid()
   ↓
current()
   ↓
...
```

So:

```text
Iterator
├── rewind()
├── valid()
├── current()
├── next()
└── key()
```

controls the iteration.

---

# 6. Understanding each method

## `rewind()`

Moves to the beginning.

```php
public function rewind(): void
{
    $this->position = 0;
}
```

---

## `current()`

Returns the current item.

```php
public function current(): mixed
{
    return $this->products[$this->position];
}
```

---

## `key()`

Returns the current key.

```php
public function key(): mixed
{
    return $this->position;
}
```

---

## `next()`

Moves to the next item.

```php
public function next(): void
{
    $this->position++;
}
```

---

## `valid()`

Checks whether the current position is valid.

```php
public function valid(): bool
{
    return isset($this->products[$this->position]);
}
```

---

# 7. The iteration lifecycle

For:

```php
foreach ($products as $product) {
}
```

think:

```text
             START
               │
               ▼
           rewind()
               │
               ▼
            valid?
          /         \
        yes          no
         │            │
         ▼            ▼
      current()      STOP
         │
         ▼
     your code
         │
         ▼
       next()
         │
         └───────────┐
                     │
                     ▼
                   valid?
```

Once you understand this, custom iterators become much easier.

---

# 8. `Iterator` vs `IteratorAggregate`

There are two important approaches.

### `Iterator`

Your class **is responsible for iteration**.

```php
final class ProductIterator implements Iterator
```

### `IteratorAggregate`

Your class **provides another iterable object**.

```php
final class ProductCollection implements IteratorAggregate
```

In application code, `IteratorAggregate` is often cleaner.

---

# 9. `IteratorAggregate`

Let's build a collection.

```php
final class ProductCollection implements IteratorAggregate
{
    public function __construct(
        private array $products,
    ) {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->products);
    }
}
```

Now:

```php
$products = new ProductCollection([
    'Laptop',
    'Keyboard',
    'Mouse',
]);

foreach ($products as $product) {
    echo $product . PHP_EOL;
}
```

Output:

```text
Laptop
Keyboard
Mouse
```

---

# 10. Why `IteratorAggregate` is often easier

With `Iterator`, you have to manage:

```text
position
current
next
rewind
valid
key
```

With `IteratorAggregate`, you can delegate that work:

```php
public function getIterator(): Traversable
{
    return new ArrayIterator($this->products);
}
```

Conceptually:

```text
ProductCollection
       ↓
getIterator()
       ↓
ArrayIterator
       ↓
foreach
```

This is often cleaner.

---

# 11. `Traversable`

You may encounter:

```php
Traversable
```

Don't try to directly implement it.

`Traversable` is a special internal interface.

Classes can be iterable by implementing:

```php
Iterator
```

or:

```php
IteratorAggregate
```

Generators also produce `Traversable` objects.

So conceptually:

```text
Traversable
├── Iterator
├── IteratorAggregate
└── Generator
```

---

# 12. SPL

Now let's talk about **SPL**.

SPL stands for:

> Standard PHP Library

It provides many built-in data structures and utility classes.

Some important ones:

```text
ArrayIterator
ArrayObject
SplQueue
SplStack
SplPriorityQueue
SplFixedArray
DirectoryIterator
RecursiveDirectoryIterator
FilesystemIterator
```

You should know what these are, even if you don't use every one regularly.

---

# 13. `ArrayIterator`

Instead of building an iterator yourself:

```php
$products = new ArrayIterator([
    'Laptop',
    'Keyboard',
    'Mouse',
]);
```

Then:

```php
foreach ($products as $product) {
    echo $product . PHP_EOL;
}
```

This is useful when you specifically need an iterator object.

---

# 14. `ArrayObject`

`ArrayObject` lets you work with array-like objects.

```php
$products = new ArrayObject([
    'Laptop',
    'Keyboard',
    'Mouse',
]);
```

You can:

```php
$products[] = 'Monitor';
```

and:

```php
foreach ($products as $product) {
    echo $product . PHP_EOL;
}
```

But don't use `ArrayObject` just because it exists.

A normal array is often simpler.

---

# 15. `SplQueue`

A queue follows:

```text
FIFO
```

Meaning:

> First In, First Out.

Imagine customers waiting at a bank:

```text
John → Sarah → Mike
 ↑
first
```

PHP provides:

```php
$queue = new SplQueue();
```

Add:

```php
$queue->enqueue('John');
$queue->enqueue('Sarah');
$queue->enqueue('Mike');
```

Remove:

```php
echo $queue->dequeue();
```

Output:

```text
John
```

Then:

```text
Sarah
Mike
```

---

# 16. `SplStack`

A stack follows:

```text
LIFO
```

Meaning:

> Last In, First Out.

Think of plates:

```text
Plate 3 ← remove first
Plate 2
Plate 1
```

PHP:

```php
$stack = new SplStack();

$stack->push('Page 1');
$stack->push('Page 2');
$stack->push('Page 3');
```

Then:

```php
echo $stack->pop();
```

Output:

```text
Page 3
```

---

# 17. Queue vs Stack

| Structure | Rule            | Example                 |
| --------- | --------------- | ----------------------- |
| Queue     | FIFO            | Job queue               |
| Stack     | LIFO            | Undo history            |
| Array     | General purpose | Most normal collections |

For Laravel applications, you will frequently encounter queue concepts, although Laravel's actual job queue system is much more sophisticated than simply using `SplQueue`.

---

# 18. `SplPriorityQueue`

Sometimes items have different priorities.

For example:

```text
Critical
High
Normal
Low
```

PHP has:

```php
SplPriorityQueue
```

Example:

```php
$queue = new SplPriorityQueue();

$queue->insert('Normal task', 1);
$queue->insert('Critical task', 10);
$queue->insert('Low task', 0);
```

The highest-priority item is retrieved first.

This is useful for understanding priority-based data structures.

---

# 19. Iterator + Generator

Here's an important connection to your previous lesson.

A generator:

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

produces an object that supports iteration.

So:

```php
foreach (numbers() as $number) {
    echo $number;
}
```

works because the generator participates in PHP's iteration system.

Conceptually:

```text
foreach
  ↓
Traversable
  ↓
Generator
  ↓
yield
```

---

# 20. Iterator vs Generator

This distinction is important.

### Iterator

You explicitly create an object implementing:

```php
Iterator
```

and manage the iteration mechanics.

### Generator

You usually just write:

```php
yield
```

and PHP manages the iterator mechanics.

Compare:

```php
final class NumberIterator implements Iterator
{
    // Lots of iterator methods...
}
```

with:

```php
function numbers(): Generator
{
    yield 1;
    yield 2;
    yield 3;
}
```

For many sequential data-processing problems, a generator is dramatically simpler.

---

# 21. When should you use `Iterator`?

Use a custom `Iterator` when your object itself needs sophisticated iteration behavior.

For example:

```text
Tree
Graph
Custom collection
Complex traversal
```

If you simply want to lazily produce values:

```text
Generator
```

is often the better choice.

---

# 22. Laravel Connection — Collections

Laravel's:

```php
Collection
```

is not the same thing as PHP's `Iterator`, but Laravel collections are designed around iterable data and provide a rich API:

```php
$users
    ->filter(...)
    ->map(...)
    ->sort(...)
    ->groupBy(...);
```

For example:

```php
$names = collect([
    'John',
    'Sarah',
    'Mike',
]);

$uppercaseNames = $names->map(
    fn (string $name): string => strtoupper($name)
);
```

Laravel gives you a much more expressive abstraction than manually implementing `Iterator`.

---

# 23. Don't Reinvent Laravel Collections

If you're working inside Laravel and need normal collection operations, don't build:

```php
MyCustomIterator
```

just to implement:

```text
map
filter
reduce
groupBy
```

Laravel's `Collection` already provides these.

Use the right abstraction:

```text
Simple data
   ↓
Array

Laravel application data
   ↓
Collection

Huge/lazy sequence
   ↓
Generator / lazy collection

Complex custom traversal
   ↓
Iterator
```

---

# 24. `LazyCollection`

This is particularly important for Laravel.

Laravel provides:

```php
LazyCollection
```

which combines collection-style operations with lazy evaluation.

For example:

```php
use Illuminate\Support\LazyCollection;

$numbers = LazyCollection::make(function () {
    for ($i = 1; $i <= 1_000_000; $i++) {
        yield $i;
    }
});
```

Then:

```php
$numbers
    ->filter(fn (int $number): bool => $number % 2 === 0)
    ->take(10)
    ->each(function (int $number): void {
        echo $number . PHP_EOL;
    });
```

The important idea:

```text
1,000,000 possible values
        ↓
Lazy processing
        ↓
Only values needed
```

This is a powerful Laravel pattern for large datasets.

---

# 25. Performance

For small data:

```php
$users = User::all();
```

may be completely fine.

For millions of records:

```php
$users = User::all();
```

can be dangerous because you may load a huge amount into memory.

Instead consider:

```php
User::query()->lazy()
```

or:

```php
User::query()->cursor()
```

depending on your workload.

The principle is:

```text
Small dataset
→ normal collection/array

Large dataset
→ chunk/lazy/cursor
```

---

# 26. Common Mistakes

### ❌ Building custom iterators unnecessarily

Don't write 50 lines of iterator code when:

```php
foreach ($array as $item)
```

already solves the problem.

---

### ❌ Confusing Iterator with Generator

Remember:

```text
Iterator
→ You implement iteration behavior.

Generator
→ PHP manages the iteration using yield.
```

---

### ❌ Loading everything first

This:

```php
$users = User::all();
```

may be a problem with a huge table.

---

### ❌ Calling `iterator_to_array()` unnecessarily

If you have:

```php
$generator
```

and do:

```php
$data = iterator_to_array($generator);
```

you've materialized everything into memory again.

---

# 27. Security

Iterators themselves aren't a security mechanism.

But they can help reduce memory pressure when processing untrusted or very large inputs.

For example:

```text
Large upload
    ↓
Stream
    ↓
Generator
    ↓
Process incrementally
```

Still enforce:

- upload size limits
- validation
- authentication
- authorization
- timeouts
- resource limits

Never assume lazy processing alone makes an operation safe.

---

# 28. Interview Questions

### Q1. What is an Iterator?

An object that provides a standard mechanism for traversing a collection.

### Q2. What methods does `Iterator` require?

```php
current()
key()
next()
rewind()
valid()
```

### Q3. What is `IteratorAggregate`?

An interface that allows an object to provide another iterable object through:

```php
getIterator()
```

### Q4. What is `Traversable`?

The internal interface representing something that can be traversed with `foreach`.

### Q5. Iterator vs Generator?

Iterator requires explicit iteration implementation.

Generator uses `yield` and PHP manages the iterator behavior.

### Q6. What is SPL?

The **Standard PHP Library**, which provides built-in data structures and utilities.

### Q7. What is `SplQueue`?

A FIFO queue.

### Q8. What is `SplStack`?

A LIFO stack.

---

# 🧪 Small Exercise

Create:

```php
final class UserCollection implements IteratorAggregate
```

It should receive:

```php
[
    'John',
    'Sarah',
    'Mike',
]
```

and allow:

```php
$users = new UserCollection([
    'John',
    'Sarah',
    'Mike',
]);

foreach ($users as $user) {
    echo $user . PHP_EOL;
}
```

Expected:

```text
John
Sarah
Mike
```

Use `ArrayIterator` internally rather than implementing all five `Iterator` methods yourself.

---

# 🔥 Challenge

Create a lazy number generator:

```php
function numbers(int $start, int $end): Generator
```

Then create a Laravel-style pipeline conceptually:

```text
numbers(1, 1_000_000)
        ↓
only even numbers
        ↓
only numbers > 500,000
        ↓
take first 10
        ↓
print
```

The important requirement is:

> Don't create a million-element intermediate array.

---

# 🎯 Summary

Your mental model should now be:

```text
foreach
   ↓
Traversable
   ├── Iterator
   ├── IteratorAggregate
   └── Generator
```

And:

```text
Array
→ simple collection

Iterator
→ custom traversal

Generator
→ lazy value production

SPL
→ built-in data structures

Laravel Collection
→ expressive application-level collection API

Laravel LazyCollection
→ collection API + lazy processing
```

### Your PHP progression

```text
PHP Fundamentals
       ↓
OOP
       ↓
SOLID
       ↓
Namespaces
       ↓
Composer
       ↓
Exceptions
       ↓
Enums
       ↓
Generators
       ↓
Attributes
       ↓
Iterators & SPL ✅
```

**Next lesson: PHP Closures & Callables** — we'll go deep into anonymous functions, arrow functions, callbacks, `use`, first-class callables, and how these power Laravel Collections, middleware, events, queues, and dependency injection.
