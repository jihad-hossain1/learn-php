# Lesson 26 — Laravel Project Structure & Request Lifecycle

Now we officially enter **Laravel Core**.

You've learned the PHP foundations and architecture. The next step is understanding what happens **inside Laravel when a user sends an HTTP request**.

This is one of the most important concepts for debugging Laravel applications.

---

# 1. Beginner Explanation

When you visit:

```text
https://example.com/todos
```

Laravel doesn't magically execute:

```php
TodoController::index();
```

There is a complete process before that happens.

At a high level:

```text
Browser
   ↓
Web Server
   ↓
public/index.php
   ↓
Laravel Application
   ↓
Middleware
   ↓
Router
   ↓
Controller
   ↓
Service
   ↓
Database
   ↓
Response
   ↓
Browser
```

Understanding this flow makes Laravel much easier to reason about.

---

# 2. Real-World Analogy

Think about entering a secure office.

```text
You
 ↓
Building entrance
 ↓
Security check
 ↓
Reception
 ↓
Correct department
 ↓
Employee
 ↓
Work performed
 ↓
Response
```

Laravel works similarly.

```text
Request
 ↓
Middleware
 ↓
Router
 ↓
Controller
 ↓
Application logic
 ↓
Response
```

---

# 3. Laravel Project Structure

A typical Laravel application looks approximately like:

```text
my-app/
│
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   └── Providers/
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
│   ├── index.php
│   └── ...
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
│
├── storage/
│
├── tests/
│
├── vendor/
│
├── .env
├── artisan
└── composer.json
```

Let's understand what these actually mean.

---

# 4. `app/`

This is where most of your application code lives.

For example:

```text
app/
├── Http/
├── Models/
├── Providers/
└── ...
```

Your business/application code generally belongs here.

---

# 5. `app/Http/Controllers`

Controllers handle HTTP requests.

Example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class TodoController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Todo list',
        ]);
    }
}
```

The controller should coordinate the request rather than contain your entire business system.

---

# 6. `app/Models`

This contains Eloquent models.

Example:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Todo extends Model
{
}
```

The model represents a database entity.

For example:

```text
Todo
User
Order
Product
Booking
```

---

# 7. `app/Http/Requests`

This is where Form Requests live.

Example:

```php
final class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
```

Remember:

```text
Request validation
        ↓
Form Request
```

---

# 8. `app/Http/Middleware`

Middleware sits around requests.

Examples:

```text
Authentication
Authorization
Rate limiting
Logging
CORS
Maintenance mode
```

A middleware can inspect a request before it reaches the controller.

---

# 9. `app/Providers`

Service Providers are where Laravel services and application bindings are registered.

For example:

```php
$this->app->bind(
    PaymentGateway::class,
    StripePayment::class
);
```

You learned this in the previous lesson.

Think:

```text
Service Provider
       ↓
Service Container
       ↓
Dependencies
```

---

# 10. `bootstrap/`

The `bootstrap` directory contains framework bootstrapping code and cached framework configuration.

The important idea is:

> Laravel uses this area to prepare the application before handling requests.

You normally don't put business logic here.

---

# 11. `config/`

Contains configuration files.

Examples:

```text
config/app.php
config/database.php
config/cache.php
config/filesystems.php
config/mail.php
```

Configuration should generally come from:

```text
config/*.php
       ↑
     .env
```

For example:

```php
'database' => [
    'host' => env('DB_HOST'),
],
```

---

# 12. `.env`

The `.env` file contains environment-specific configuration.

Example:

```text
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=my_app
DB_USERNAME=root
DB_PASSWORD=
```

### Security rule ⚠️

Never commit secrets such as:

```text
API keys
Database passwords
Private credentials
Production secrets
```

to Git.

`.env` should normally be excluded from version control.

---

# 13. `database/`

Contains:

```text
migrations/
factories/
seeders/
```

We'll go deeply into this later.

Think:

```text
Migrations
   ↓
Database structure

Factories
   ↓
Fake/test data

Seeders
   ↓
Initial/application data
```

---

# 14. `routes/`

This defines application routes.

For example:

```php
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/todos', [
    TodoController::class,
    'index',
]);
```

Conceptually:

```text
GET /todos
    ↓
TodoController@index
```

---

# 15. `resources/`

Contains frontend resources.

For example:

```text
resources/
├── views/
├── css/
└── js/
```

Blade templates usually live in:

```text
resources/views/
```

Example:

```text
resources/views/todos/index.blade.php
```

---

# 16. `storage/`

Used for generated/runtime files.

Examples:

```text
logs
cache
uploaded files
compiled Blade files
```

Don't confuse:

```text
storage/
```

with:

```text
public/
```

We'll cover Laravel's filesystem and storage system later.

---

# 17. `public/`

This is extremely important.

The web server should point to:

```text
public/
```

not the project root.

Inside:

```text
public/index.php
```

is the application's main HTTP entry point.

---

# 18. `vendor/`

Created by Composer.

Contains:

```text
Laravel framework
Third-party packages
Composer autoloader
```

For example:

```text
vendor/autoload.php
```

You generally don't edit files inside `vendor`.

---

# 19. `composer.json`

This defines your PHP dependencies and Composer configuration.

Example:

```json
{
  "require": {
    "php": "^8.3"
  }
}
```

Laravel itself and third-party packages are managed through Composer.

---

# 20. `artisan`

Laravel's command-line interface.

Examples:

```bash
php artisan migrate
```

```bash
php artisan make:model Todo
```

```bash
php artisan route:list
```

```bash
php artisan cache:clear
```

Think of Artisan as:

> **Laravel's command center.**

---

# 21. Now the Important Part — Request Lifecycle

Let's follow a request.

Suppose the browser sends:

```http
GET /todos
```

The process begins.

---

## Step 1 — Web Server

The browser sends:

```text
GET /todos
```

to your web server.

For example:

```text
Nginx
```

or:

```text
Apache
```

The server is configured to use Laravel's:

```text
public/
```

directory.

---

# 22. Step 2 — `public/index.php`

The request enters:

```text
public/index.php
```

This is Laravel's HTTP entry point.

Conceptually:

```text
Browser
   ↓
Web Server
   ↓
public/index.php
```

Laravel then bootstraps the application.

---

# 23. Step 3 — Composer Autoloader

Laravel uses Composer's autoloader.

Conceptually:

```php
require __DIR__.'/../vendor/autoload.php';
```

This allows PHP to locate classes automatically.

For example:

```php
App\Models\Todo
```

can be loaded without manually requiring:

```php
require 'Todo.php';
```

You learned why this works when we studied **Namespaces + Composer + PSR-4**.

---

# 24. Step 4 — Laravel Application Bootstrap

Laravel creates/bootstraps the application.

Conceptually:

```text
Application
    ↓
Service Container
    ↓
Configuration
    ↓
Service Providers
```

This prepares the framework.

---

# 25. Step 5 — Service Providers

Laravel registers application/framework services.

For example:

```text
Database
Cache
Queue
Filesystem
Events
Application services
```

Your own providers can register bindings:

```php
$this->app->bind(
    PaymentGateway::class,
    StripePayment::class
);
```

This is where your previous lesson becomes relevant.

---

# 26. Step 6 — HTTP Kernel / Middleware Pipeline

The request goes through Laravel's HTTP middleware pipeline.

Think:

```text
Request
   ↓
Middleware 1
   ↓
Middleware 2
   ↓
Middleware 3
   ↓
Router
```

Middleware can:

- inspect the request
- modify the request
- reject the request
- call the next layer
- modify the response

---

# 27. Middleware Analogy

Imagine airport security:

```text
Passenger
    ↓
ID check
    ↓
Security check
    ↓
Boarding validation
    ↓
Gate
```

If security fails:

```text
Passenger
    ↓
Security ❌
    ↓
STOP
```

Laravel middleware works similarly.

---

# 28. Step 7 — Router

Laravel's router determines which route matches.

Example:

```php
Route::get('/todos', [
    TodoController::class,
    'index',
]);
```

Request:

```text
GET /todos
```

matches:

```text
TodoController@index
```

---

# 29. Route Parameters

Consider:

```php
Route::get('/todos/{todo}', [
    TodoController::class,
    'show',
]);
```

Request:

```text
GET /todos/25
```

Laravel extracts:

```text
todo = 25
```

Then it can perform route model binding.

For example:

```php
public function show(Todo $todo): JsonResponse
{
    return response()->json($todo);
}
```

Laravel can resolve the `Todo` model for you.

We'll study this deeply when we cover routing and Eloquent.

---

# 30. Step 8 — Controller

The request reaches:

```php
public function index(): JsonResponse
```

The controller performs HTTP-level coordination.

For example:

```php
public function index(): JsonResponse
{
    $todos = $this->todoService->list();

    return response()->json($todos);
}
```

Laravel's Service Container can resolve:

```text
TodoController
      ↓
TodoService
```

---

# 31. Step 9 — Business Logic

The controller calls:

```php
$this->todoService->list();
```

Then:

```text
TodoService
      ↓
Repository
      ↓
Eloquent
      ↓
Database
```

This connects directly to our previous lesson.

---

# 32. Step 10 — Database

Eloquent executes a query.

Conceptually:

```sql
SELECT *
FROM todos;
```

The database returns the records.

Then:

```text
Database
   ↓
Eloquent
   ↓
Repository
   ↓
Service
   ↓
Controller
```

---

# 33. Step 11 — Response

The controller creates:

```php
return response()->json($todos);
```

Laravel generates an HTTP response:

```http
HTTP/1.1 200 OK
Content-Type: application/json
```

with JSON data.

---

# 34. Step 12 — Middleware Runs Back

This part is often misunderstood.

Middleware wraps the request.

Conceptually:

```text
        Request
           ↓
    ┌─────────────┐
    │ Middleware  │
    │      ↓      │
    │   Router    │
    │      ↓      │
    │ Controller  │
    │      ↓      │
    │  Response   │
    └─────────────┘
           ↓
        Response
```

Middleware can perform work **after** the controller too.

For example:

```text
Request
 ↓
Start timer
 ↓
Controller
 ↓
Response
 ↓
Calculate execution time
 ↓
Return response
```

---

# 35. Complete Lifecycle

Memorize this mental model:

```text
             HTTP REQUEST
                  │
                  ▼
             Web Server
                  │
                  ▼
         public/index.php
                  │
                  ▼
          Composer Autoload
                  │
                  ▼
          Laravel Bootstrap
                  │
                  ▼
         Service Providers
                  │
                  ▼
          Middleware Pipeline
                  │
                  ▼
                Router
                  │
                  ▼
              Controller
                  │
                  ▼
               Service
                  │
                  ▼
             Repository
                  │
                  ▼
               Eloquent
                  │
                  ▼
              Database
                  │
                  ▼
               Response
                  │
                  ▼
          Middleware Pipeline
                  │
                  ▼
               Browser
```

This diagram is worth remembering.

---

# 36. Why Understanding Lifecycle Matters

Suppose you're debugging:

> "Why isn't my controller being called?"

You can work backwards:

```text
Controller
   ↑
Router
   ↑
Middleware
   ↑
Bootstrap
   ↑
Entry point
```

Maybe:

```text
Route doesn't match
```

or:

```text
Middleware rejects request
```

or:

```text
Authentication fails
```

or:

```text
Exception happens before controller
```

Understanding lifecycle makes debugging systematic.

---

# 37. Where Does Laravel Resolve Dependencies?

Suppose:

```php
final class TodoController
{
    public function __construct(
        private TodoService $todoService
    ) {
    }
}
```

Laravel sees:

```text
TodoController
      ↓
TodoService
```

If `TodoService` requires:

```php
TodoRepository
```

then:

```text
TodoController
      ↓
TodoService
      ↓
TodoRepository
      ↓
EloquentTodoRepository
```

The Service Container builds the dependency graph.

---

# 38. A Practical Request

Imagine:

```http
POST /todos
```

with:

```json
{
  "title": "Learn Laravel"
}
```

The flow becomes:

```text
POST /todos
      ↓
public/index.php
      ↓
Laravel
      ↓
Middleware
      ↓
Route
      ↓
StoreTodoRequest
      ↓
Authorization
      ↓
Validation
      ↓
TodoController
      ↓
CreateTodoData
      ↓
TodoService
      ↓
TodoRepository
      ↓
Todo Model
      ↓
Database
      ↓
Todo created
      ↓
JSON Response
```

Now you're thinking about Laravel as a system instead of individual files.

---

# 39. Common Mistakes

### ❌ Pointing Nginx/Apache to the project root

Bad:

```text
/var/www/my-app
```

Better:

```text
/var/www/my-app/public
```

This protects application files from direct web access.

---

### ❌ Putting business logic in routes

Avoid:

```php
Route::post('/orders', function () {
    // 100 lines of business logic
});
```

Use a controller/service architecture for meaningful application behavior.

---

### ❌ Putting secrets in source code

Bad:

```php
$stripeSecret = 'sk_live_...';
```

Use environment/configuration management.

---

### ❌ Editing `vendor/`

Don't modify package code directly.

Your changes will disappear when dependencies are reinstalled/updated.

---

### ❌ Thinking middleware is only authentication

Middleware can handle many cross-cutting concerns:

```text
Authentication
Authorization
Rate limiting
Logging
Headers
Maintenance
Request transformation
```

---

# 40. Performance Considerations

Laravel request overhead exists, but the largest performance problems usually come from:

```text
Database queries
N+1 queries
External API calls
Large payloads
Poor caching
Heavy synchronous work
```

For example:

```text
Request
 ↓
Controller
 ↓
100 database queries ❌
```

is far more concerning than:

```text
Request
 ↓
Controller
```

having another method call.

Later we'll optimize these systematically.

---

# 41. Security

The most important security concept from today's lesson:

> **Only expose the `public` directory to the web.**

Your project contains things like:

```text
.env
composer.json
storage
app
config
```

These should not be directly accessible through HTTP.

Correct:

```text
Web Server
    ↓
public/
```

Incorrect:

```text
Web Server
    ↓
Entire Laravel project
```

---

# 42. Interview Questions

### Q1. What is Laravel's front controller?

```text
public/index.php
```

It acts as the main HTTP entry point.

---

### Q2. Why should the web server point to `public/`?

To expose only publicly accessible files and protect application internals.

---

### Q3. What does middleware do?

It provides a layer around request/response processing.

---

### Q4. What does the router do?

Maps an incoming request to a route/action.

---

### Q5. What is a Service Provider?

A place where services/bindings are registered and framework/application components are bootstrapped.

---

### Q6. What is the Service Container?

Laravel's dependency-resolution system.

---

### Q7. Where should business logic live?

Generally in appropriate application/domain services rather than controllers.

---

# 43. Small Exercise 📝

Draw the request lifecycle yourself.

For:

```text
GET /todos/10
```

Create an ASCII diagram containing:

```text
Browser
Web Server
public/index.php
Middleware
Router
Controller
Service
Repository
Eloquent
Database
Response
```

Then answer:

### Question 1

What happens if middleware rejects the request?

### Question 2

What happens if the route doesn't exist?

### Question 3

Where does Laravel resolve the Controller's dependencies?

### Question 4

Why should the web server point to `public/`?

---

# 44. Challenge 🔥

Build a simple route:

```php
Route::get('/todos/{todo}', [
    TodoController::class,
    'show',
]);
```

Then implement:

```php
final class TodoController
{
    public function __construct(
        private TodoService $todoService
    ) {
    }

    public function show(Todo $todo): JsonResponse
    {
        // ...
    }
}
```

Your goal is to understand this complete flow:

```text
GET /todos/10
      ↓
Router
      ↓
Route Model Binding
      ↓
TodoController
      ↓
TodoService
      ↓
Response
```

Don't worry about building the complete application yet.

Focus on understanding **what happens at each stage**.

---

# 45. Summary

Today you learned two major things.

### Laravel structure

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vendor/
```

### Laravel request lifecycle

```text
Request
   ↓
Web Server
   ↓
public/index.php
   ↓
Bootstrap
   ↓
Service Providers
   ↓
Middleware
   ↓
Router
   ↓
Controller
   ↓
Service
   ↓
Repository
   ↓
Eloquent
   ↓
Database
   ↓
Response
   ↓
Browser
```

The most important mental model is:

> **Laravel is a pipeline. A request enters through `public/index.php`, passes through framework bootstrapping and middleware, gets matched by the router, reaches your application code, and eventually becomes an HTTP response.**

### Next Lesson → **Laravel Routing**

We'll go deeply into:

```text
GET
POST
PUT
PATCH
DELETE
Route Parameters
Optional Parameters
Named Routes
Route Groups
Route Model Binding
Middleware Groups
Route Constraints
Resource Controllers
API Routes
Route Caching
```

and you'll build the routing layer for our **Todo application**.
