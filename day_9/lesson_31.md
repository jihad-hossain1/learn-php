# Lesson 31 — Laravel Service Container & Service Providers

This is one of the **most important Laravel concepts** for becoming a professional backend engineer.

You already learned Dependency Injection in Lesson 24. Now we're going deeper into **how Laravel actually creates and manages your dependencies**.

---

# 1. Beginner Explanation

Imagine your application needs:

```text
TodoController
    ↓ needs
TodoService
    ↓ needs
TodoRepository
    ↓ needs
Todo model
```

You could manually create everything:

```php
$repository = new TodoRepository();
$service = new TodoService($repository);
$controller = new TodoController($service);
```

But in a real Laravel application, you don't want controllers responsible for constructing their dependencies.

Laravel has a **Service Container** that acts like a dependency manager.

```text
              Laravel Service Container
                       │
          ┌────────────┼────────────┐
          ↓            ↓            ↓
     TodoService   PaymentService  MailService
          ↓
   TodoRepository
```

You tell Laravel:

> "Whenever somebody needs `TodoRepository`, give them this implementation."

Laravel then resolves the dependency automatically.

---

# 2. Real-World Analogy

Think about a restaurant.

You order:

> "Give me a burger."

You don't care:

- where the meat came from
- who prepared it
- which employee cooked it
- which kitchen equipment was used

The restaurant handles those dependencies.

Laravel's container works similarly.

Your controller says:

```php
public function __construct(
    TodoService $todoService
) {
    $this->todoService = $todoService;
}
```

You don't write:

```php
$this->todoService = new TodoService(
    new TodoRepository()
);
```

Laravel handles it.

---

# 3. Raw PHP — Without a Container

Suppose we have:

```php
declare(strict_types=1);

interface TodoRepository
{
    public function find(int $id): ?Todo;
}
```

Implementation:

```php
final class EloquentTodoRepository implements TodoRepository
{
    public function find(int $id): ?Todo
    {
        return Todo::find($id);
    }
}
```

Service:

```php
final class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {
    }

    public function find(int $id): ?Todo
    {
        return $this->repository->find($id);
    }
}
```

Controller:

```php
final class TodoController
{
    private TodoService $service;

    public function __construct()
    {
        $repository = new EloquentTodoRepository();

        $this->service = new TodoService(
            $repository
        );
    }
}
```

This works.

But the controller is now responsible for object creation.

That's undesirable.

---

# 4. What We Want

We want:

```php
final class TodoController
{
    public function __construct(
        private TodoService $service,
    ) {
    }
}
```

The controller only says:

> I need `TodoService`.

Laravel figures out how to construct it.

---

# 5. Laravel Service Container

Laravel's container manages dependencies.

Conceptually:

```text
TodoController
      │
      │ needs
      ↓
TodoService
      │
      │ needs
      ↓
TodoRepository
      │
      │ implementation
      ↓
EloquentTodoRepository
```

Laravel recursively resolves the dependency graph.

---

# 6. Automatic Resolution

Suppose:

```php
final class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {
    }
}
```

If Laravel knows:

```text
TodoRepository
        ↓
EloquentTodoRepository
```

it can create:

```text
TodoService
    ↓
EloquentTodoRepository
```

automatically.

---

# 7. `bind()`

The most important container operation is:

```php
$this->app->bind(
    TodoRepository::class,
    EloquentTodoRepository::class
);
```

This means:

> Whenever Laravel asks for `TodoRepository`, provide `EloquentTodoRepository`.

For example:

```php
$this->app->bind(
    PaymentGateway::class,
    StripePaymentGateway::class
);
```

Now:

```php
final class OrderService
{
    public function __construct(
        private PaymentGateway $paymentGateway,
    ) {
    }
}
```

Laravel can resolve:

```text
PaymentGateway
       ↓
StripePaymentGateway
```

---

# 8. Why Interfaces Are Useful

This is where Dependency Inversion becomes powerful.

Instead of:

```php
final class OrderService
{
    public function __construct(
        private StripePaymentGateway $gateway,
    ) {
    }
}
```

you can depend on:

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
        private PaymentGateway $gateway,
    ) {
    }
}
```

Laravel decides the implementation.

```text
                PaymentGateway
                      │
             ┌────────┴────────┐
             ↓                 ↓
      StripeGateway      PayPalGateway
```

This makes your application easier to change and test.

---

# 9. Service Providers

So where should we put these bindings?

Usually in a **Service Provider**.

Laravel applications contain providers under:

```text
app/Providers/
```

A common place for application-specific bindings is:

```text
AppServiceProvider
```

For example:

```php
namespace App\Providers;

use App\Contracts\TodoRepository;
use App\Repositories\EloquentTodoRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TodoRepository::class,
            EloquentTodoRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
```

---

# 10. `register()` vs `boot()`

This is extremely important.

## `register()`

Use it for:

> Registering things in the container.

Example:

```php
public function register(): void
{
    $this->app->bind(
        TodoRepository::class,
        EloquentTodoRepository::class
    );
}
```

Think:

```text
REGISTER = "Tell Laravel what exists."
```

---

## `boot()`

`boot()` runs after service providers have been registered.

It's commonly used for setup that depends on the application being bootstrapped.

For example:

```php
public function boot(): void
{
    //
}
```

Some Laravel features commonly configured during boot include:

- view composers
- model observers
- custom validation extensions
- other framework/application initialization

Mental model:

```text
register()
    ↓
"Register dependencies"

boot()
    ↓
"Application is now being initialized"
```

---

# 11. `bind()` vs `singleton()`

This distinction is important.

## `bind()`

Every resolution can create a new instance.

```php
$this->app->bind(
    PaymentGateway::class,
    StripePaymentGateway::class
);
```

Conceptually:

```text
resolve()
   ↓
new StripePaymentGateway()

resolve()
   ↓
new StripePaymentGateway()
```

Different instances can be created.

---

# 12. `singleton()`

A singleton keeps one instance in the container for the relevant application lifecycle.

```php
$this->app->singleton(
    PaymentGateway::class,
    StripePaymentGateway::class
);
```

Conceptually:

```text
First resolve
     ↓
Create object
     ↓
Store object
     ↓
Return object

Second resolve
     ↓
Return same managed instance
```

Useful for things that should be shared rather than recreated.

Examples might include:

- stateless application clients
- expensive-to-create infrastructure objects
- certain application-wide services

But don't blindly make everything a singleton.

---

# 13. `scoped()`

Modern Laravel also provides:

```php
$this->app->scoped(
    SomeService::class,
    SomeImplementation::class
);
```

A scoped binding is shared within a particular application lifecycle/scope and is especially useful in environments where an application instance can handle multiple requests or jobs over its lifetime.

Conceptually:

```text
Request A
    ↓
Scoped instance A

Request B
    ↓
Scoped instance B
```

This is different from treating an object as globally shared forever.

For request/job-specific state, `scoped()` can be important.

---

# 14. Closure Bindings

You can also tell Laravel how to construct something manually.

```php
$this->app->bind(
    PaymentGateway::class,
    function ($app) {
        return new StripePaymentGateway(
            config('services.stripe.secret')
        );
    }
);
```

Now Laravel executes the closure when resolving the dependency.

This is useful when construction requires configuration or other dependencies.

---

# 15. Contextual Binding

Sometimes the same interface needs different implementations depending on who is requesting it.

Example:

```text
OrderService
      ↓
PaymentGateway → Stripe

RefundService
      ↓
PaymentGateway → PayPal
```

You can use contextual binding.

Conceptually:

```php
$this->app
    ->when(OrderService::class)
    ->needs(PaymentGateway::class)
    ->give(StripePaymentGateway::class);
```

And:

```php
$this->app
    ->when(RefundService::class)
    ->needs(PaymentGateway::class)
    ->give(PayPalPaymentGateway::class);
```

Now Laravel understands the context.

---

# 16. Primitive Dependencies

Suppose:

```php
final class ReportService
{
    public function __construct(
        private string $reportPath,
    ) {
    }
}
```

Laravel doesn't automatically know what value:

```php
$reportPath
```

should contain.

You can configure primitive dependencies explicitly using container configuration/contextual binding.

In practice, for application configuration, it's often cleaner to inject a configuration-derived value or a dedicated configuration object rather than scattering primitive strings throughout your services.

---

# 17. `app()` Helper

Laravel provides:

```php
app(TodoService::class);
```

which asks the container to resolve the service.

You can also see:

```php
app()->make(TodoService::class);
```

Conceptually:

```text
app()
 ↓
Service Container
 ↓
Resolve dependency
 ↓
Return object
```

---

# 18. But Don't Abuse `app()`

This is bad:

```php
final class TodoController
{
    public function store()
    {
        $service = app(TodoService::class);

        return $service->create(...);
    }
}
```

Prefer:

```php
final class TodoController
{
    public function __construct(
        private TodoService $service,
    ) {
    }

    public function store()
    {
        return $this->service->create(...);
    }
}
```

Why?

Because constructor injection makes dependencies explicit.

### Bad

```text
Controller
   │
   └── secretly depends on TodoService
```

### Better

```text
Controller
   │
   └── constructor clearly says:
       "I need TodoService"
```

This improves:

- readability
- testing
- maintainability
- dependency visibility

---

# 19. Your Todo Application

Let's connect everything you've learned.

### Contract

```php
interface TodoRepository
{
    public function create(array $data): Todo;
}
```

### Implementation

```php
final class EloquentTodoRepository implements TodoRepository
{
    public function create(array $data): Todo
    {
        return Todo::create($data);
    }
}
```

### Service

```php
final class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {
    }

    public function create(
        CreateTodoData $data
    ): Todo {
        return $this->repository->create([
            'title' => $data->title,
            'user_id' => $data->userId,
        ]);
    }
}
```

### Provider

```php
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TodoRepository::class,
            EloquentTodoRepository::class
        );
    }
}
```

### Controller

```php
final class TodoController
{
    public function __construct(
        private TodoService $todoService,
    ) {
    }
}
```

Laravel sees:

```text
TodoController
      ↓
TodoService
      ↓
TodoRepository
      ↓
EloquentTodoRepository
```

and resolves the entire graph.

---

# 20. Behind the Scenes

Suppose Laravel needs:

```php
TodoController
```

The container roughly performs:

```text
1. Need TodoController
        ↓
2. Inspect constructor
        ↓
3. Need TodoService
        ↓
4. Inspect TodoService constructor
        ↓
5. Need TodoRepository
        ↓
6. Check container binding
        ↓
7. TodoRepository → EloquentTodoRepository
        ↓
8. Create EloquentTodoRepository
        ↓
9. Create TodoService
        ↓
10. Create TodoController
```

This is the dependency graph resolution you should understand as a backend engineer.

---

# 21. Service Container vs Service Provider

These are often confused.

| Concept              | Purpose                                    |
| -------------------- | ------------------------------------------ |
| Service Container    | Resolves/manages dependencies              |
| Service Provider     | Registers/configures services              |
| Dependency Injection | Supplies dependencies to classes           |
| Binding              | Tells container what implementation to use |

Think:

```text
Service Provider
       │
       │ registers
       ↓
Service Container
       │
       │ resolves
       ↓
Dependencies
       │
       ↓
Your Classes
```

---

# 22. Request Lifecycle Connection

You previously learned:

```text
Browser
   ↓
public/index.php
   ↓
Laravel bootstrap
   ↓
Service Providers
   ↓
Middleware
   ↓
Router
   ↓
Controller
```

Now add the container:

```text
Browser
   ↓
public/index.php
   ↓
Laravel bootstrap
   ↓
Service Providers
   ↓
Service Container
   ↓
Middleware
   ↓
Router
   ↓
Controller
   ↓
Container resolves dependencies
   ↓
Service
   ↓
Repository
   ↓
Database
```

This explains why Service Providers are part of Laravel's startup/bootstrapping process.

---

# 23. Testing Benefit

This is one of the biggest advantages.

Suppose production uses:

```php
StripePaymentGateway
```

During a test, you might use:

```php
FakePaymentGateway
```

You can replace the binding:

```php
$this->app->bind(
    PaymentGateway::class,
    FakePaymentGateway::class
);
```

Your service doesn't need to change.

```text
Production
PaymentGateway
      ↓
StripePaymentGateway


Testing
PaymentGateway
      ↓
FakePaymentGateway
```

This is why programming against interfaces can be valuable.

---

# 24. Common Mistakes

### Mistake 1 — Constructing dependencies manually

```php
new TodoService(
    new EloquentTodoRepository()
);
```

inside controllers.

Prefer DI.

---

### Mistake 2 — Using `app()` everywhere

```php
app(TodoService::class)
```

This hides dependencies.

Prefer constructor injection when possible.

---

### Mistake 3 — Making everything a singleton

Don't assume:

```php
singleton()
```

is automatically better.

Shared mutable state can create difficult bugs.

---

### Mistake 4 — Putting application logic inside providers

Avoid:

```php
public function register(): void
{
    // complex business logic
}
```

Providers should configure/register services, not become business-service classes.

---

### Mistake 5 — Confusing Service with Service Provider

These are completely different.

```text
TodoService
    = business/application logic

TodoServiceProvider
    = application bootstrapping/configuration
```

---

# 25. Performance

The container adds some resolution work, but don't prematurely optimize by avoiding dependency injection.

Focus first on expensive operations:

```text
Database queries
External APIs
Large datasets
N+1 queries
Network calls
File operations
```

A clean dependency graph is usually much more valuable than manually constructing objects to save tiny amounts of container overhead.

---

# 26. Security

The container itself isn't an authorization system.

Don't assume:

```php
app(AdminService::class)
```

means the current user is an admin.

Authorization belongs in appropriate layers:

```text
Authentication
      ↓
Authorization
      ↓
Business logic
```

For example:

```php
$request->user()->can(...)
```

or policies/gates where appropriate.

---

# 27. When NOT to Use Custom Container Bindings

You don't need:

```php
$this->app->bind(...)
```

for every class.

If Laravel can automatically resolve a concrete class:

```php
final class TodoService
{
    public function __construct(
        private TodoRepository $repository,
    ) {}
}
```

you may only need an explicit binding for the interface:

```php
TodoRepository::class
    ↓
EloquentTodoRepository::class
```

Don't create unnecessary abstractions just to make the architecture look complicated.

---

# 28. Professional Architecture

Your Todo application can now look like:

```text
HTTP
 │
 ↓
Route
 │
 ↓
Middleware
 │
 ↓
Form Request
 │
 ↓
Controller
 │
 ↓
DTO
 │
 ↓
Service
 │
 ↓
Repository Interface
 │
 ↓
Service Container
 │
 ↓
Repository Implementation
 │
 ↓
Eloquent
 │
 ↓
Database
```

This is the architecture you should start thinking in.

---

# 29. Interview Questions

### Q1. What is Laravel's Service Container?

**Answer:**

> Laravel's Service Container is a dependency management system that resolves and manages class dependencies through dependency injection and bindings.

---

### Q2. What is a Service Provider?

> A Service Provider is where application/framework services are registered and bootstrapped.

---

### Q3. Difference between `bind()` and `singleton()`?

```text
bind()
→ resolves according to the binding and may create new instances

singleton()
→ shares the same managed instance for the applicable container lifecycle
```

---

### Q4. What is dependency injection?

> Supplying a class's dependencies from outside the class rather than having the class construct them itself.

---

### Q5. Why use interfaces?

They allow code to depend on abstractions rather than concrete implementations.

---

### Q6. What is contextual binding?

It allows Laravel to provide different implementations of the same dependency depending on which class is requesting it.

---

# 30. Small Exercise

Create:

```text
Logger
```

Interface:

```php
interface Logger
{
    public function log(string $message): void;
}
```

Create:

```text
FileLogger
```

and:

```text
DatabaseLogger
```

Then register:

```text
Logger
   ↓
FileLogger
```

in `AppServiceProvider`.

Create:

```php
final class UserService
{
    public function __construct(
        private Logger $logger,
    ) {
    }
}
```

Then let Laravel resolve `UserService`.

---

# 31. Challenge Exercise 🔥

Build this:

```text
OrderController
       ↓
OrderService
       ↓
PaymentGateway
       ↓
StripePaymentGateway
```

Create:

```php
interface PaymentGateway
{
    public function charge(float $amount): bool;
}
```

Then:

```php
final class StripePaymentGateway implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
```

Bind:

```text
PaymentGateway
       ↓
StripePaymentGateway
```

using the service container.

Then inject:

```php
PaymentGateway
```

into:

```php
OrderService
```

**Do not instantiate `StripePaymentGateway` inside `OrderService`.**

That's the important part.

---

# 32. The Most Important Mental Model

Remember these four things:

```text
Dependency Injection
        ↓
"Give my class what it needs."

Service Container
        ↓
"I know how to create/manage those dependencies."

Service Provider
        ↓
"I configure/register those dependencies."

Binding
        ↓
"When you ask for X, give you Y."
```

And your Laravel architecture becomes:

```text
Interface
    ↓
Container Binding
    ↓
Implementation
    ↓
Service
    ↓
Controller
```

### Key rule

> **Don't make your classes responsible for creating their dependencies. Let Laravel's container manage the dependency graph.**

That principle will become extremely important when we reach **testing, queues, events, APIs, and production architecture**.
