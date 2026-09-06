# 🚀 PHP 8.3 — Lesson 19: Attributes

Today we're learning **PHP Attributes**.

Attributes are a modern PHP feature that lets us attach **metadata** to classes, methods, properties, parameters, and other code elements.

They become especially interesting when you start working with **Laravel, dependency injection, validation, routing, serialization, and framework internals**.

---

# 1. What is an Attribute?

Imagine you have a class:

```php
final class User
{
    // ...
}
```

Now imagine you want to attach information to it:

```text
User
 ├── Entity
 ├── Table: users
 └── Cacheable
```

PHP Attributes let you write metadata directly above the code:

```php
#[Entity]
final class User
{
}
```

The syntax is:

```php
#[Something]
```

The `#[...]` syntax is called an **attribute**.

---

# 2. Real-world analogy

Think about a product in a warehouse.

The product itself might be:

```text
Laptop
```

But it can have labels:

```text
Fragile
Electronics
Keep Dry
High Value
```

Those labels don't change what the laptop **is**.

They provide **metadata about it**.

PHP Attributes work similarly.

```php
#[Cacheable]
final class Product
{
}
```

The class is still a `Product`.

`Cacheable` is metadata describing how another system should treat it.

---

# 3. Why do we need Attributes?

Before PHP Attributes, developers often used:

### Comments

```php
/**
 * @Route("/users")
 */
public function users()
{
}
```

### Strings/configuration

```php
$routes = [
    'users' => '/users',
];
```

### Constants

```php
class User
{
    public const TABLE = 'users';
}
```

These approaches can work, but they aren't strongly integrated into the PHP language.

Modern PHP gives us:

```php
#[Route('/users')]
public function users()
{
}
```

This is structured metadata that PHP can inspect using **Reflection**.

---

# 4. Creating your first Attribute

First, we create an attribute class.

```php
<?php

declare(strict_types=1);

#[Attribute]
final class Example
{
}
```

Now we can use it:

```php
#[Example]
final class User
{
}
```

That's it.

But there is an important detail:

```php
#[Attribute]
```

above `Example` tells PHP:

> This class is allowed to be used as an attribute.

---

# 5. Importing Attribute

Usually you'll write:

```php
use Attribute;
```

Complete example:

```php
<?php

declare(strict_types=1);

use Attribute;

#[Attribute]
final class Example
{
}
```

Then:

```php
#[Example]
final class User
{
}
```

---

# 6. Attributes can contain values

This is where they become much more useful.

```php
#[Attribute]
final class Route
{
    public function __construct(
        public string $path,
    ) {
    }
}
```

Now:

```php
#[Route('/users')]
final class UserController
{
}
```

We attached:

```text
path = /users
```

to the class.

---

# 7. Multiple values

Attributes can accept multiple constructor arguments.

```php
#[Attribute]
final class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
    ) {
    }
}
```

Use:

```php
#[Route('/users', 'POST')]
public function store(): void
{
}
```

Or named arguments:

```php
#[Route(
    path: '/users',
    method: 'POST',
)]
public function store(): void
{
}
```

Named arguments are often easier to read.

---

# 8. Attribute targets

By default, an attribute can have restrictions.

For example:

```php
#[Attribute(Attribute::TARGET_CLASS)]
final class Entity
{
}
```

This means `Entity` can only be used on classes.

Correct:

```php
#[Entity]
final class User
{
}
```

Incorrect:

```php
final class User
{
    #[Entity]
    public string $name;
}
```

PHP will reject the invalid usage.

---

# 9. Multiple targets

You can allow several targets.

```php
#[Attribute(
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_PROPERTY
)]
final class Example
{
}
```

Now it can be used on:

```php
#[Example]
final class User
{
    #[Example]
    public string $name;

    #[Example]
    public function save(): void
    {
    }
}
```

---

# 10. Repeatable Attributes

Normally you may not want the same attribute multiple times.

But you can explicitly allow it.

```php
#[Attribute(
    Attribute::TARGET_METHOD |
    Attribute::IS_REPEATABLE
)]
final class Permission
{
    public function __construct(
        public string $name,
    ) {
    }
}
```

Now:

```php
#[Permission('users.read')]
#[Permission('users.write')]
public function update(): void
{
}
```

This method has two permissions.

---

# 11. Reading Attributes with Reflection

Creating attributes is only half the story.

The important question is:

> How does our application read them?

PHP provides **Reflection**.

Example:

```php
#[Route('/users')]
final class UserController
{
}
```

Read it:

```php
$reflection = new ReflectionClass(UserController::class);

$attributes = $reflection->getAttributes();
```

Now:

```php
foreach ($attributes as $attribute) {
    echo $attribute->getName();
}
```

Output:

```text
Route
```

---

# 12. Reading Attribute Arguments

Remember:

```php
#[Route('/users')]
```

We want to retrieve:

```text
/users
```

Use:

```php
$attributes = $reflection->getAttributes(Route::class);

foreach ($attributes as $attribute) {
    $arguments = $attribute->getArguments();

    var_dump($arguments);
}
```

You would get something equivalent to:

```php
[
    '/users',
]
```

---

# 13. Creating the Attribute Object

Reflection can instantiate the attribute:

```php
$attribute = $attributes[0]->newInstance();
```

Now:

```php
echo $attribute->path;
```

Output:

```text
/users
```

This is powerful.

The process is:

```text
#[Route('/users')]
       ↓
PHP Reflection
       ↓
Route metadata
       ↓
newInstance()
       ↓
Route object
       ↓
path = /users
```

---

# 14. Complete Raw PHP Example

Let's build a tiny routing system.

### Attribute

```php
<?php

declare(strict_types=1);

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
    ) {
    }
}
```

### Controller

```php
final class UserController
{
    #[Route('/users', 'GET')]
    public function index(): void
    {
        echo 'List users';
    }

    #[Route('/users', 'POST')]
    public function store(): void
    {
        echo 'Create user';
    }
}
```

### Reflection

```php
$reflection = new ReflectionClass(UserController::class);

foreach ($reflection->getMethods() as $method) {
    $attributes = $method->getAttributes(Route::class);

    foreach ($attributes as $attribute) {
        $route = $attribute->newInstance();

        echo $method->getName() . PHP_EOL;
        echo $route->method . ' ' . $route->path . PHP_EOL;
    }
}
```

Output:

```text
index
GET /users

store
POST /users
```

We've essentially built a tiny piece of framework behavior.

---

# 15. Why Reflection matters

Attributes by themselves don't automatically execute anything.

This:

```php
#[Route('/users')]
```

doesn't magically create a route.

Something must read the metadata.

Usually:

```text
Attribute
   ↓
Reflection
   ↓
Framework reads metadata
   ↓
Framework performs behavior
```

This is a critical concept.

---

# 16. Attributes vs Annotations

Older PHP frameworks commonly used annotations like:

```php
/**
 * @Route("/users")
 */
```

Modern PHP can use:

```php
#[Route('/users')]
```

Conceptually:

```text
Old
Comment/documentation
        ↓
Framework parser


Modern
PHP Attribute
        ↓
Reflection
```

Attributes are part of the PHP language rather than merely specially formatted comments.

---

# 17. Laravel Perspective

Laravel uses attributes in various modern features, but you should **not assume every Laravel feature should be converted to attributes**.

Laravel traditionally uses conventions, configuration, service providers, route definitions, middleware, and other mechanisms heavily.

For example, normal Laravel routing remains very common:

```php
Route::get('/users', [UserController::class, 'index']);
```

You should understand attributes as a **PHP language feature** and then learn where Laravel uses them appropriately.

---

# 18. Attribute + Dependency Injection

Here's an interesting architectural example.

Suppose we define:

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final class InjectConfig
{
    public function __construct(
        public string $key,
    ) {
    }
}
```

Then:

```php
final class PaymentService
{
    public function __construct(
        #[InjectConfig('payment.currency')]
        private string $currency,
    ) {
    }
}
```

A dependency injection container could inspect the constructor parameter and see:

```text
Parameter
   ↓
#[InjectConfig('payment.currency')]
   ↓
Read configuration
   ↓
Inject value
```

This is one reason attributes are interesting for framework/container design.

---

# 19. Attributes on Properties

You can target properties:

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column
{
    public function __construct(
        public string $name,
    ) {
    }
}
```

Use:

```php
final class User
{
    #[Column('user_name')]
    public string $name;
}
```

Reflection can inspect:

```php
$reflection = new ReflectionClass(User::class);

$property = $reflection->getProperty('name');

$attributes = $property->getAttributes(Column::class);
```

---

# 20. Attributes on Parameters

You can also target parameters:

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final class FromRequest
{
}
```

Then:

```php
public function store(
    #[FromRequest]
    string $name,
): void {
}
```

A framework could inspect the parameter and decide where its value should come from.

---

# 21. Attributes on Methods

Very common use case:

```php
#[Attribute(Attribute::TARGET_METHOD)]
final class Cache
{
    public function __construct(
        public int $seconds,
    ) {
    }
}
```

Then:

```php
#[Cache(300)]
public function getProducts(): array
{
    // ...
}
```

A framework/library could read:

```text
Cache
seconds = 300
```

and apply caching behavior.

---

# 22. Attribute Design with Interfaces

We can combine attributes with interfaces.

For example:

```php
interface Cacheable
{
}
```

Then:

```php
#[Attribute(Attribute::TARGET_METHOD)]
final class Cache implements Cacheable
{
    public function __construct(
        public int $seconds,
    ) {
    }
}
```

This is useful when building extensible systems.

However, don't create abstractions simply because you can.

Remember our SOLID lesson:

> Good architecture solves a real problem.

---

# 23. Attributes and Validation

Imagine:

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required
{
}
```

Then:

```php
final class RegisterUserData
{
    #[Required]
    public string $name;

    #[Required]
    public string $email;
}
```

A validation system could use Reflection:

```text
DTO
 ↓
Reflection
 ↓
Find #[Required]
 ↓
Check property
 ↓
Validation result
```

This is conceptually similar to how many frameworks/libraries use metadata.

---

# 24. Important: Attributes don't validate anything

This code:

```php
#[Required]
public string $name;
```

doesn't automatically validate `$name`.

An attribute is metadata.

You need code that interprets the metadata.

This distinction is very important:

```text
Attribute
≠
Behavior

Attribute
→ describes something

Framework/library
→ interprets it
```

---

# 25. Attribute Classes Should Usually Be Small

Good:

```php
#[Attribute(Attribute::TARGET_METHOD)]
final class Cache
{
    public function __construct(
        public int $seconds,
    ) {
    }
}
```

Avoid turning an attribute into a giant business-logic class:

```php
#[Attribute]
final class Cache
{
    // 500 lines of business logic ❌
}
```

The attribute should generally describe metadata.

The actual behavior belongs elsewhere.

This follows our earlier separation-of-responsibilities principles.

---

# 26. Security Considerations

Attributes themselves aren't a security mechanism.

But if you're building a system that executes behavior based on attributes, be careful.

For example:

```php
#[Permission('admin')]
public function delete(): void
{
}
```

You still need the actual authorization system to enforce it.

Never assume:

```php
#[Permission('admin')]
```

automatically protects anything unless the application actually checks it.

The safe architecture is:

```text
Request
 ↓
Authentication
 ↓
Authorization
 ↓
Business logic
```

Attributes may provide metadata to the authorization system, but they don't replace authorization.

---

# 27. Performance

Reflection has a cost.

If you repeatedly do:

```php
new ReflectionClass(...)
```

and inspect hundreds of attributes on every request, that can become expensive.

Frameworks can optimize this through:

- caching
- compiled metadata
- boot-time discovery
- container caching

So in production systems:

```text
Reflection
   ↓
Discover metadata
   ↓
Cache result
   ↓
Reuse
```

is often preferable to repeatedly scanning everything.

---

# 28. Common Mistakes

### ❌ Mistake 1: Thinking attributes execute automatically

```php
#[Route('/users')]
```

doesn't automatically register a route.

Something must read it.

---

### ❌ Mistake 2: Putting business logic inside attributes

Keep attributes focused on metadata.

---

### ❌ Mistake 3: Using attributes everywhere

Don't replace every configuration mechanism with attributes.

Sometimes this is clearer:

```php
Route::get(...);
```

than creating a custom attribute system.

---

### ❌ Mistake 4: Ignoring caching

Reflection-heavy metadata discovery can become expensive at scale.

---

### ❌ Mistake 5: Confusing attribute name and instance

This:

```php
$attribute->getName();
```

returns the attribute class name.

This:

```php
$attribute->newInstance();
```

creates the actual attribute object.

---

# 29. Behind the Scenes

When PHP sees:

```php
#[Route('/users')]
public function index(): void
{
}
```

PHP stores the attribute metadata.

Then Reflection can inspect it:

```text
PHP source
   │
   ▼
#[Route('/users')]
   │
   ▼
PHP metadata
   │
   ▼
ReflectionMethod
   │
   ▼
getAttributes()
   │
   ▼
newInstance()
   │
   ▼
Route object
```

The framework/library can then decide what to do.

---

# 30. Production Architecture

Suppose you're building a framework feature.

A clean architecture could look like:

```text
Attribute
   │
   │ metadata
   ▼
Reflection Scanner
   │
   ▼
Metadata Registry
   │
   ▼
Application Service
   │
   ▼
Actual Behavior
```

Don't make the attribute itself responsible for the entire workflow.

---

# 🧪 Small Exercise

Create an attribute:

```php
#[Cache(300)]
```

where `300` represents seconds.

Your class should look like:

```php
final class ProductService
{
    #[Cache(300)]
    public function getProducts(): array
    {
        return [];
    }
}
```

Then use Reflection to print:

```text
Method: getProducts
Cache seconds: 300
```

---

# 🔥 Challenge

Build these three attributes:

```php
#[Get('/users')]
#[Post('/users')]
#[Auth]
```

Then create:

```php
final class UserController
{
    #[Get('/users')]
    #[Auth]
    public function index(): void
    {
    }

    #[Post('/users')]
    #[Auth]
    public function store(): void
    {
    }
}
```

Use Reflection to produce:

```text
index
GET /users
Authentication required

store
POST /users
Authentication required
```

This challenge will make the concept much more concrete.

---

# 🎯 Interview Questions

### 1. What are PHP Attributes?

Structured metadata attached to PHP code elements.

### 2. Which PHP feature is commonly used to read Attributes?

**Reflection API.**

### 3. Do Attributes automatically execute?

No.

They provide metadata. Another piece of code must interpret them.

### 4. How do you retrieve attributes from a class?

```php
$reflection->getAttributes();
```

### 5. How do you instantiate an attribute?

```php
$attribute->newInstance();
```

### 6. How do you restrict where an attribute can be used?

Using:

```php
Attribute::TARGET_CLASS
Attribute::TARGET_METHOD
Attribute::TARGET_PROPERTY
Attribute::TARGET_PARAMETER
```

and other target constants.

### 7. What does `IS_REPEATABLE` do?

Allows the same attribute to appear multiple times on the same target.

---

# 🧠 Final Mental Model

You should remember Attributes like this:

```text
             PHP Attribute
                  │
                  ▼
            Metadata
                  │
                  ▼
             Reflection
                  │
                  ▼
       Framework / Application
                  │
                  ▼
              Behavior
```

The important distinction is:

```text
Attribute = Information
Code      = Behavior
```

This separation becomes very useful when you start studying framework internals.

---

## ✅ What you've learned so far

Your PHP fundamentals are now becoming fairly advanced:

```text
PHP
├── Types
├── Arrays
├── Functions
├── OOP
├── Interfaces
├── Abstract Classes
├── Traits
├── SOLID
├── Namespaces
├── Composer / Autoloading
├── Exceptions
├── Enums
├── Generators
└── Attributes ✅
```

### Next lesson

**PHP Advanced OOP: Iterators & SPL**

We'll learn how PHP's `Iterator`, `IteratorAggregate`, `ArrayIterator`, `Countable`, and other SPL tools work—and how they connect directly to the generators and collections you've just learned.
