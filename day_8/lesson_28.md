# Lesson 28 — Laravel Middleware 🔐

Middleware is one of the most important concepts in Laravel because it sits **between the incoming request and your application logic**.

You already know this basic lifecycle:

```text
Browser
   ↓
Request
   ↓
Middleware
   ↓
Router / Controller
   ↓
Service
   ↓
Database
   ↓
Response
```

Today we'll understand exactly **what middleware does, why it exists, how it works internally, and how to create production-quality middleware**.

---

# 1. Beginner Explanation

A middleware is a piece of code that can **inspect, modify, allow, or reject an HTTP request** before it reaches your controller.

For example:

> "Only logged-in users can access `/dashboard`."

Instead of putting this inside every controller:

```php
if (! auth()->check()) {
    // reject
}
```

you create/use middleware:

```text
Request
   ↓
auth middleware
   ↓
Is user authenticated?
   ├── No  → Reject
   └── Yes
        ↓
    Controller
```

This prevents duplication.

---

# 2. Real-World Analogy 🛂

Imagine an airport.

```text
Passenger
   ↓
Security Check
   ↓
Passport Check
   ↓
Boarding Gate
   ↓
Plane
```

Each checkpoint can decide:

- allow the person through
- stop them
- modify/check something
- redirect them somewhere

Laravel middleware works similarly:

```text
HTTP Request
     ↓
Middleware
     ↓
Middleware
     ↓
Controller
     ↓
Response
```

---

# 3. Simple Example

Imagine:

```text
GET /dashboard
```

We want only authenticated users.

Conceptually:

```php
public function handle(Request $request, Closure $next)
{
    if (! auth()->check()) {
        return redirect('/login');
    }

    return $next($request);
}
```

The important line is:

```php
return $next($request);
```

It means:

> "The request passed my check. Continue to the next layer."

---

# 4. The Most Important Middleware Concept

Think of middleware as a pipeline.

```text
Request
   ↓
Middleware A
   ↓
Middleware B
   ↓
Middleware C
   ↓
Controller
   ↓
Response
```

But there's something very important:

Middleware can execute code **before AND after** `$next()`.

Example:

```php
public function handle(Request $request, Closure $next)
{
    // BEFORE controller

    $response = $next($request);

    // AFTER controller

    return $response;
}
```

So middleware behaves like:

```text
          BEFORE
             ↓
Request → Middleware
             ↓
          $next()
             ↓
        Controller
             ↓
          Response
             ↓
          AFTER
             ↓
        Middleware
```

This is a powerful concept.

---

# 5. Raw PHP Example

Let's understand the idea without Laravel.

```php
<?php

declare(strict_types=1);

function authMiddleware(
    array $request,
    callable $next
): array {
    if (! $request['authenticated']) {
        return [
            'status' => 401,
            'body' => 'Unauthorized',
        ];
    }

    return $next($request);
}

$response = authMiddleware(
    ['authenticated' => true],
    function (array $request): array {
        return [
            'status' => 200,
            'body' => 'Dashboard',
        ];
    }
);

print_r($response);
```

The important idea:

```text
Middleware
    ↓
check something
    ↓
$next()
    ↓
next handler
```

Laravel formalizes this pattern.

---

# 6. Laravel Middleware Structure

A middleware typically looks like:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckSomething
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        // Check request

        return $next($request);
    }
}
```

Let's understand each part.

---

## `Request`

```php
Request $request
```

contains information about the incoming HTTP request:

```text
URL
HTTP method
headers
query parameters
body
cookies
IP
etc.
```

---

## `$next`

```php
Closure $next
```

represents the next stage in the pipeline.

Calling:

```php
$next($request);
```

means:

> Continue processing this request.

---

# 7. Creating Middleware

Use Artisan:

```bash
php artisan make:middleware CheckUserStatus
```

Laravel creates:

```text
app/
└── Http/
    └── Middleware/
        └── CheckUserStatus.php
```

Example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckUserStatus
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if ($request->user()?->is_active !== true) {
            abort(403);
        }

        return $next($request);
    }
}
```

Now:

```text
Request
   ↓
CheckUserStatus
   ↓
active?
 ┌───────┴───────┐
 No              Yes
 ↓                ↓
403            Controller
```

---

# 8. Registering Middleware

Modern Laravel uses the application bootstrap configuration to configure middleware.

In Laravel applications using the current structure, you'll commonly configure middleware in:

```text
bootstrap/app.php
```

For example:

```php
->withMiddleware(function (Middleware $middleware): void {
    //
})
```

You can configure aliases and middleware groups there.

This is different from older Laravel versions where developers commonly edited:

```text
app/Http/Kernel.php
```

So when working with Laravel 12+, don't blindly follow tutorials written for older Laravel versions.

---

# 9. Middleware Alias

Instead of writing the entire class everywhere, give middleware an alias.

Conceptually:

```php
$middleware->alias([
    'active' => CheckUserStatus::class,
]);
```

Then:

```php
Route::middleware('active')->group(function () {
    //
});
```

Now your route reads nicely:

```text
Route
 ↓
active middleware
 ↓
Controller
```

---

# 10. Route Middleware

You can apply middleware to a single route.

```php
Route::get('/dashboard', [
    DashboardController::class,
    'index',
])->middleware('auth');
```

Meaning:

```text
GET /dashboard
      ↓
auth
      ↓
DashboardController
```

---

# 11. Middleware Groups

Suppose every admin page requires:

```text
authentication
admin permission
request logging
```

Instead of:

```php
Route::get(...)->middleware([
    'auth',
    'admin',
    'logging',
]);
```

on every route, use a group:

```php
Route::middleware([
    'auth',
    'admin',
    'logging',
])->group(function () {

    Route::get('/admin/dashboard', ...);

    Route::get('/admin/users', ...);

    Route::get('/admin/orders', ...);

});
```

This gives:

```text
/admin/*
   ↓
auth
   ↓
admin
   ↓
logging
   ↓
Controller
```

---

# 12. Middleware Parameters

Middleware can accept parameters.

Imagine:

```text
/admin
```

should require the `admin` role.

But:

```text
/editor
```

should require the `editor` role.

You can create:

```php
Route::get('/admin', ...)
    ->middleware('role:admin');
```

and:

```php
Route::get('/editor', ...)
    ->middleware('role:editor');
```

The middleware can receive the parameter:

```php
public function handle(
    Request $request,
    Closure $next,
    string $role
): Response {
    // Check $role

    return $next($request);
}
```

So:

```text
role:admin
     ↓
$role = "admin"
```

---

# 13. Multiple Middleware Parameters

You can also pass multiple values:

```php
->middleware('role:admin,manager')
```

Then:

```php
public function handle(
    Request $request,
    Closure $next,
    string ...$roles
): Response {
    //
}
```

Now:

```php
$roles
```

contains:

```php
[
    'admin',
    'manager',
]
```

---

# 14. Middleware Can Stop Requests

This is one of its most important responsibilities.

Example:

```php
public function handle(
    Request $request,
    Closure $next
): Response {
    if (! $request->user()) {
        abort(401);
    }

    return $next($request);
}
```

Notice:

```php
return $next($request);
```

isn't executed when the user isn't authenticated.

Therefore:

```text
Request
   ↓
Middleware
   ↓
NOT authenticated
   ↓
401
   X
Controller never executes
```

This is useful for:

- authentication
- authorization checks
- rate limiting
- maintenance mode
- request validation
- tenant checks
- security policies

---

# 15. Middleware Can Modify Requests

Middleware isn't limited to rejecting requests.

It can modify information before passing the request forward.

Conceptually:

```php
$request->merge([
    'source' => 'web',
]);

return $next($request);
```

Then the controller can access:

```php
$request->input('source');
```

### But be careful

Don't use middleware to inject arbitrary business data just because it is convenient.

For example, don't turn middleware into:

```text
Middleware
 ↓
Load product
 ↓
Calculate price
 ↓
Create order
 ↓
Send email
```

That's business logic.

Middleware should generally deal with **cross-cutting HTTP concerns**.

---

# 16. What Is a Cross-Cutting Concern?

This is an important software architecture concept.

Suppose you have:

```text
TodoController
OrderController
UserController
ProductController
```

All of them need authentication.

Instead of:

```text
TodoController → auth code
OrderController → auth code
UserController → auth code
ProductController → auth code
```

use:

```text
             ┌→ TodoController
             │
Request → Auth Middleware
             │
             ├→ OrderController
             │
             ├→ UserController
             │
             └→ ProductController
```

Authentication is a **cross-cutting concern**.

Middleware is designed for this kind of problem.

---

# 17. Authentication vs Authorization

Very important distinction:

### Authentication

> Who are you?

```text
Is the user logged in?
```

Usually:

```php
auth
```

### Authorization

> Are you allowed to perform this action?

For example:

```text
User 10 owns Todo 50?
```

That's usually handled with:

- Policies
- Gates
- authorization middleware where appropriate

Don't confuse:

```text
authenticated
```

with:

```text
authorized
```

---

# 18. Middleware + Controller + Service

Our architecture now becomes:

```text
                 HTTP Request
                      ↓
                 Middleware
                      ↓
                 Controller
                      ↓
                    DTO
                      ↓
                   Service
                      ↓
                 Repository
                      ↓
                  Database
```

Example:

```php
Route::post('/todos', [
    TodoController::class,
    'store',
])->middleware('auth');
```

Controller:

```php
public function store(
    StoreTodoRequest $request
): JsonResponse {
    $todo = $this->todoService->create(
        CreateTodoData::fromRequest($request)
    );

    return response()->json($todo, 201);
}
```

Notice:

```text
auth
```

isn't inside:

```text
TodoService
```

Why?

Because authentication is an HTTP/request concern.

The service should contain business logic and ideally shouldn't care whether the request came from:

```text
Web
API
CLI
Queue
Test
```

That's clean architecture.

---

# 19. Middleware Execution Order

Suppose:

```php
Route::middleware([
    'auth',
    'admin',
    'logging',
])->get('/admin', ...);
```

Think of it approximately as:

```text
Request
   ↓
auth
   ↓
admin
   ↓
logging
   ↓
Controller
   ↓
logging
   ↓
admin
   ↓
auth
   ↓
Response
```

This is sometimes called **onion-style middleware execution**.

Why?

Because middleware surrounds the next layer:

```text
       auth
    ┌─────────┐
    │  admin  │
    │ ┌─────┐ │
    │ │ log │ │
    │ │ ┌─┐ │ │
    │ │ │C│ │ │
    │ │ └─┘ │ │
    │ └─────┘ │
    └─────────┘
```

---

# 20. Before and After Example

```php
public function handle(
    Request $request,
    Closure $next
): Response {
    logger()->info('Request started');

    $response = $next($request);

    logger()->info('Request finished');

    return $response;
}
```

Execution:

```text
Request started
       ↓
Controller
       ↓
Request finished
```

This makes middleware useful for things like:

- timing
- logging
- adding headers
- modifying responses

---

# 21. Response Modification

You can modify a response after `$next()`.

Example:

```php
public function handle(
    Request $request,
    Closure $next
): Response {
    $response = $next($request);

    $response->headers->set(
        'X-App-Version',
        '1.0'
    );

    return $response;
}
```

Now every response passing through that middleware can receive:

```text
X-App-Version: 1.0
```

This demonstrates the two-way nature of middleware.

---

# 22. Terminable Middleware

Laravel also supports middleware that can perform work after the response has been sent, using a `terminate()` method.

Conceptually:

```php
public function terminate(
    Request $request,
    Response $response
): void {
    // Post-response work
}
```

This can be useful for certain logging or cleanup tasks.

But don't assume that `terminate()` makes expensive work magically free.

For genuinely expensive work such as:

```text
send email
generate report
process image
call slow external API
```

prefer a queue.

```text
Request
   ↓
Middleware
   ↓
Controller
   ↓
Dispatch Job
   ↓
Response
   ↓
Queue Worker
```

We'll study queues later.

---

# 23. Middleware Request Lifecycle

Let's put everything together.

Imagine:

```text
POST /todos
```

### Step 1

Web server sends request to Laravel.

```text
POST /todos
```

### Step 2

Laravel bootstraps.

### Step 3

Router identifies:

```php
Route::post('/todos', ...)
```

### Step 4

Middleware executes:

```text
auth
```

### Step 5

Authentication succeeds.

### Step 6

Controller executes.

```php
TodoController@store
```

### Step 7

Controller calls service.

```text
TodoService
```

### Step 8

Service persists Todo.

### Step 9

Response travels back through middleware.

```text
Service
 ↓
Controller
 ↓
Middleware
 ↓
HTTP Response
```

---

# 24. Real Production Example

Suppose we're building an e-commerce application.

Request:

```text
POST /orders
```

Potential middleware:

```text
Request
   ↓
auth
   ↓
verified
   ↓
throttle
   ↓
tenant
   ↓
Controller
   ↓
OrderService
```

Each layer has a specific responsibility.

### `auth`

Is the user logged in?

### `verified`

Has the user verified their account?

### `throttle`

Is the client sending too many requests?

### `tenant`

Which organization/store does this request belong to?

### Controller

Coordinate application flow.

### Service

Execute business logic.

That's a professional separation of concerns.

---

# 25. Common Mistakes ❌

## Mistake 1 — Putting business logic in middleware

Bad:

```php
public function handle(...)
{
    $order = Order::create(...);

    Payment::charge(...);

    Mail::send(...);

    return $next(...);
}
```

Middleware shouldn't become a hidden service layer.

---

## Mistake 2 — Forgetting `$next()`

Bad:

```php
public function handle(
    Request $request,
    Closure $next
): Response {
    logger()->info('Request');

    // Forgot $next()
}
```

The request pipeline stops.

If the middleware intentionally blocks the request, that's fine.

Otherwise:

```php
return $next($request);
```

is essential.

---

## Mistake 3 — Doing database queries unnecessarily

Don't make every request perform:

```php
User::find(...)
```

inside middleware unless that middleware actually needs it.

Middleware executes frequently.

Unnecessary queries here can become expensive.

---

## Mistake 4 — Putting authorization everywhere

Don't create dozens of random middleware classes for domain-specific authorization when a Policy is the appropriate abstraction.

For example:

```text
TodoPolicy
OrderPolicy
PostPolicy
```

are often better for resource authorization.

---

# 26. Performance Considerations ⚡

Middleware runs on every request that passes through it.

Therefore, this:

```php
public function handle(...)
{
    // 20 database queries
}
```

can become very expensive.

If 1,000 requests hit that middleware:

```text
20 × 1,000
=
20,000 queries
```

Potentially disastrous.

### Good middleware

```text
small
focused
fast
predictable
```

### Bad middleware

```text
large
database-heavy
business-heavy
external-API-heavy
```

---

# 27. Security Considerations 🔐

Middleware can provide important protection.

Examples:

```text
Authentication
Authorization
Rate limiting
CSRF protection
Tenant isolation
Security headers
Maintenance mode
```

But remember:

> **Security should be enforced at the correct layer, not merely hidden inside routing.**

For example, don't trust:

```php
$request->input('user_id')
```

to determine who owns a resource.

The authenticated identity should come from the authentication system:

```php
$request->user()
```

and ownership/permission should be verified through authorization logic.

---

# 28. Middleware and API Security

Imagine:

```text
POST /api/orders
```

You might have:

```text
auth:sanctum
   ↓
throttle
   ↓
Controller
```

Then:

```text
Unauthenticated request
       ↓
auth:sanctum
       ↓
401 Unauthorized
```

The controller doesn't need to repeatedly ask:

```php
if (! auth()->check()) {
    ...
}
```

---

# 29. Middleware vs Service vs Policy

This distinction is very important for interviews and real projects.

| Component    | Main responsibility             |
| ------------ | ------------------------------- |
| Middleware   | HTTP/request pipeline concerns  |
| Controller   | Coordinate request/response     |
| Service      | Business logic                  |
| Repository   | Data-access abstraction         |
| Policy       | Resource authorization          |
| Form Request | HTTP validation + authorization |
| Model        | Domain/data representation      |

Example:

```text
"Is user logged in?"
→ Middleware

"Is this user allowed to update this Todo?"
→ Policy

"How should Todo completion work?"
→ Service

"Validate title is required"
→ Form Request

"Save Todo"
→ Eloquent/Repository
```

---

# 30. A Professional Example

Suppose we have:

```text
POST /todos/10/complete
```

Route:

```php
Route::post('/todos/{todo}/complete', [
    TodoController::class,
    'complete',
])->middleware('auth');
```

Pipeline:

```text
Request
   ↓
auth middleware
   ↓
Route Model Binding
   ↓
TodoController
   ↓
TodoPolicy
   ↓
TodoService
   ↓
Database
   ↓
Response
```

Each component has one job.

That's the architecture you should aim for.

---

# 31. When Should You NOT Use Middleware?

Don't create middleware simply because:

> "I need somewhere to put this code."

Ask:

### Is it an HTTP pipeline concern?

Use middleware.

### Is it business logic?

Use a Service/domain layer.

### Is it resource authorization?

Use Policy/Gate.

### Is it request validation?

Use Form Request.

### Is it data access?

Use Eloquent/Repository where justified.

This question will prevent a lot of bad Laravel architecture.

---

# 32. Interview Questions 🎯

### Q1. What is middleware?

Middleware filters or processes HTTP requests before/after they reach the application handler.

---

### Q2. What does `$next($request)` do?

It passes the request to the next stage of the middleware pipeline.

---

### Q3. Can middleware execute code after the controller?

Yes.

```php
$response = $next($request);

// After controller

return $response;
```

---

### Q4. Can middleware stop a request?

Yes.

It can return a response directly:

```php
return response()->json([
    'message' => 'Unauthorized',
], 401);
```

The controller won't execute.

---

### Q5. Middleware vs Policy?

Middleware generally handles request-level/cross-cutting concerns.

Policy handles authorization for a specific resource/action.

---

### Q6. Middleware vs Service?

Middleware handles HTTP pipeline concerns.

Service handles business/application logic.

---

### Q7. Why shouldn't middleware contain expensive operations?

Because middleware may execute on many requests and can become a performance bottleneck.

---

### Q8. What is middleware ordering?

The order in which middleware wraps and processes the request/response pipeline.

---

# 33. Small Exercise 📝

Create:

```text
CheckUserActive
```

Middleware.

Requirement:

```text
If user is not authenticated
    → 401

If authenticated but inactive
    → 403

If authenticated and active
    → continue
```

Expected flow:

```text
Request
   ↓
CheckUserActive
   ↓
Authenticated?
 ├── No → 401
 ↓ Yes
Active?
 ├── No → 403
 ↓ Yes
$next()
 ↓
Controller
```

Try implementing:

```php
public function handle(
    Request $request,
    Closure $next
): Response
{
    //
}
```

---

# 34. Challenge 🚀

Build an `EnsureAdmin` middleware.

Route:

```php
Route::get('/admin/dashboard', [
    AdminDashboardController::class,
    'index',
])->middleware(['auth', 'admin']);
```

Requirements:

### Guest

```text
→ authentication middleware
→ 401/redirect
```

### Logged-in normal user

```text
→ admin middleware
→ 403
```

### Admin

```text
→ controller
```

Architecture:

```text
                    Request
                       ↓
                      auth
                       ↓
                 authenticated?
                   /       \
                 No         Yes
                 ↓           ↓
              Reject       admin
                              ↓
                         administrator?
                          /          \
                        No            Yes
                        ↓              ↓
                      403         Controller
```

---

# 35. Most Important Mental Model

Remember this:

```text
                 Laravel Request
                       ↓
             ┌─────────────────┐
             │   Middleware    │
             │                 │
             │ Can inspect     │
             │ Can reject      │
             │ Can modify      │
             │ Can continue    │
             │ Can modify      │
             │ response        │
             └────────┬────────┘
                      ↓
                  Controller
                      ↓
                   Service
                      ↓
                  Database
```

And the key code:

```php
$response = $next($request);
```

means:

```text
"Continue the pipeline."
```

---

# 36. Summary

You should now understand:

```text
Middleware
   ↓
HTTP request pipeline
```

Important concepts:

- `handle()`
- `$request`
- `$next`
- before/after middleware
- route middleware
- middleware aliases
- middleware groups
- middleware parameters
- authentication middleware
- authorization vs authentication
- response modification
- terminable middleware
- middleware ordering
- middleware performance
- middleware security
- middleware vs Service vs Policy vs Form Request

### Professional Laravel architecture

```text
HTTP Request
     ↓
Middleware
     ↓
Form Request
     ↓
Controller
     ↓
DTO
     ↓
Service
     ↓
Repository / Eloquent
     ↓
Database
     ↓
Response
```

The key principle:

> **Middleware should protect and process the request pipeline—not become a hidden business-logic layer.**

---

## Next Lesson → Lesson 29: Laravel Controllers & Form Requests

We'll connect routing + middleware with the next layer and go deeply into:

```text
Route
  ↓
Middleware
  ↓
Controller
  ↓
Form Request
  ↓
DTO
  ↓
Service
```

including **thin controllers, dependency injection, controller actions, Form Request validation, authorization, validation lifecycle, custom rules, error responses, and production API patterns**.
