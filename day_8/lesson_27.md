# Lesson 27 — Laravel Routing 🚦

You already learned the **Laravel request lifecycle**. Now we go one layer deeper:

> **Routing is the system that decides which code should handle an incoming HTTP request.**

Think of Laravel routing as the **traffic controller** of your application.

```text
HTTP Request
     ↓
   Router
     ↓
Which route matches?
     ↓
Middleware
     ↓
Controller / Closure
     ↓
Response
```

---

# 1. Beginner Explanation

Suppose a user visits:

```text
GET /todos
```

Laravel needs to know:

> "When someone requests `/todos` using GET, what should I execute?"

You define that in a route:

```php
Route::get('/todos', function () {
    return 'Todo list';
});
```

Now:

```text
GET /todos
     ↓
Route matches
     ↓
Closure executes
     ↓
"Todo list"
```

---

# 2. Real-World Analogy

Imagine a large office building.

Someone arrives and says:

> "I need the accounting department."

The receptionist checks:

```text
Request: Accounting
       ↓
Receptionist
       ↓
Find department
       ↓
Send visitor there
```

Laravel's router does essentially the same thing:

```text
Request: GET /todos
       ↓
Laravel Router
       ↓
Find matching route
       ↓
TodoController@index
```

---

# 3. Where Routes Live

For web applications, routes commonly live in:

```text
routes/
├── web.php
└── api.php
```

However, in modern Laravel applications, API routing is not necessarily enabled in every fresh installation. You can install API routing with:

```bash
php artisan install:api
```

Your primary web routes are generally defined in:

```text
routes/web.php
```

Example:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/todos', function () {
    return 'Todo list';
});
```

---

# 4. Basic HTTP Methods

HTTP provides several important methods.

| Method | Typical purpose           |
| ------ | ------------------------- |
| GET    | Retrieve data             |
| POST   | Create data               |
| PUT    | Replace/update resource   |
| PATCH  | Partially update resource |
| DELETE | Delete resource           |

Example:

```php
Route::get('/todos', ...);

Route::post('/todos', ...);

Route::put('/todos/{todo}', ...);

Route::patch('/todos/{todo}', ...);

Route::delete('/todos/{todo}', ...);
```

A REST-style Todo API might look like:

```text
GET     /todos
POST    /todos
GET     /todos/{todo}
PUT     /todos/{todo}
PATCH   /todos/{todo}
DELETE  /todos/{todo}
```

---

# 5. GET Route

The simplest route:

```php
Route::get('/hello', function () {
    return 'Hello Laravel';
});
```

Visit:

```text
GET /hello
```

Response:

```text
Hello Laravel
```

### Important

A route isn't necessarily a controller.

You can technically use a closure:

```php
Route::get('/hello', function () {
    return 'Hello';
});
```

But for business logic, don't build your application this way.

Prefer:

```text
Route
 ↓
Controller
 ↓
Service
```

---

# 6. POST Route

POST is commonly used to create something.

```php
Route::post('/todos', function () {
    return 'Create Todo';
});
```

For example:

```text
POST /todos
```

with:

```json
{
  "title": "Learn Laravel"
}
```

Eventually your route should point to a controller:

```php
use App\Http\Controllers\TodoController;

Route::post('/todos', [TodoController::class, 'store']);
```

---

# 7. Route Parameters

Suppose we want:

```text
/todos/10
```

where `10` is the Todo ID.

```php
Route::get('/todos/{id}', function (int $id) {
    return "Todo ID: {$id}";
});
```

Request:

```text
GET /todos/10
```

Laravel passes:

```php
$id = 10;
```

---

# 8. Multiple Parameters

You can have multiple parameters:

```php
Route::get('/users/{user}/todos/{todo}', function (
    int $user,
    int $todo
) {
    return "User {$user}, Todo {$todo}";
});
```

Request:

```text
/users/5/todos/20
```

Results in:

```text
User 5, Todo 20
```

---

# 9. Optional Parameters

Sometimes a parameter is optional.

```php
Route::get('/users/{name?}', function (?string $name = null) {
    return $name ?? 'Guest';
});
```

Now both work:

```text
/users
```

and:

```text
/users/john
```

The `?` means:

> This parameter may be absent.

### Important

Optional route parameters should have a default value in your PHP callback:

```php
?string $name = null
```

---

# 10. Route Constraints

Suppose Todo IDs must be numeric.

You don't want this:

```text
/todos/hello
```

to match:

```php
/todos/{id}
```

You can constrain the parameter:

```php
Route::get('/todos/{id}', function (int $id) {
    return "Todo {$id}";
})->whereNumber('id');
```

Now:

```text
/todos/10
```

matches.

But:

```text
/todos/hello
```

doesn't.

---

## Other Constraints

You can use:

```php
->whereNumber('id')
```

or:

```php
->whereAlpha('name')
```

or:

```php
->whereAlphaNumeric('username')
```

or regular expressions:

```php
Route::get('/products/{slug}', function (string $slug) {
    //
})->where('slug', '[a-z0-9-]+');
```

---

# 11. Named Routes ⭐

This is extremely important in Laravel.

Instead of hardcoding URLs everywhere:

```php
Route::get('/todos', [TodoController::class, 'index']);
```

give the route a name:

```php
Route::get('/todos', [TodoController::class, 'index'])
    ->name('todos.index');
```

Now you can refer to it by name:

```php
route('todos.index');
```

For example:

```php
$url = route('todos.index');
```

---

# Why Named Routes?

Imagine your URL is:

```text
/todos
```

Six months later you change it to:

```text
/my-tasks
```

If your application contains:

```php
'/todos'
```

in 100 places, you have a maintenance problem.

But if everything uses:

```php
route('todos.index')
```

you change the route definition once.

```text
Route name
    ↓
URL
```

This is one of the reasons named routes are preferred.

---

# 12. Named Routes With Parameters

```php
Route::get('/todos/{todo}', [TodoController::class, 'show'])
    ->name('todos.show');
```

Generate URL:

```php
$url = route('todos.show', [
    'todo' => 10,
]);
```

Result:

```text
/todos/10
```

---

# 13. Route Groups

Suppose you have:

```text
/admin/users
/admin/orders
/admin/products
```

You don't want to repeatedly write:

```php
/admin
```

Use a group.

```php
Route::prefix('admin')->group(function () {

    Route::get('/users', ...);

    Route::get('/orders', ...);

    Route::get('/products', ...);

});
```

Laravel generates:

```text
/admin/users
/admin/orders
/admin/products
```

---

# 14. Name Prefixes

You can also group route names.

```php
Route::name('admin.')->group(function () {

    Route::get('/users', ...)
        ->name('users');

    Route::get('/orders', ...)
        ->name('orders');

});
```

Names become:

```text
admin.users
admin.orders
```

You can combine prefix + name:

```php
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users', ...)
            ->name('users');

        Route::get('/orders', ...)
            ->name('orders');

    });
```

Now:

```text
URL:
 /admin/users

Route name:
 admin.users
```

---

# 15. Middleware Groups

You can also apply middleware to an entire group.

```php
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', ...);

    Route::get('/todos', ...);

    Route::get('/profile', ...);

});
```

Meaning:

```text
Request
   ↓
auth middleware
   ↓
Route
```

If the user isn't authenticated, the middleware can stop the request before your controller executes.

We'll study middleware deeply in the **next lesson**.

---

# 16. Route Model Binding ⭐⭐⭐

This is one of the most important Laravel routing features.

Without route model binding:

```php
Route::get('/todos/{id}', function (int $id) {

    $todo = Todo::findOrFail($id);

    return $todo;
});
```

You manually find the model.

Laravel can do this for you.

```php
use App\Models\Todo;

Route::get('/todos/{todo}', function (Todo $todo) {
    return $todo;
});
```

Request:

```text
GET /todos/10
```

Laravel effectively does:

```php
Todo::findOrFail(10);
```

and injects the model.

---

# 17. Why Is This Powerful?

Compare:

### Without binding

```php
public function show(int $id)
{
    $todo = Todo::findOrFail($id);

    return $todo;
}
```

### With binding

```php
public function show(Todo $todo)
{
    return $todo;
}
```

Cleaner.

The controller doesn't need to manually retrieve the model.

---

# 18. Behind the Scenes

Request:

```text
GET /todos/10
```

Route:

```php
Route::get('/todos/{todo}', [
    TodoController::class,
    'show',
]);
```

Controller:

```php
public function show(Todo $todo)
{
    //
}
```

Laravel sees:

```text
{todo}
   ↓
Todo $todo
```

and resolves the model.

Conceptually:

```text
URL /todos/10
       ↓
Router
       ↓
Parameter = 10
       ↓
Todo model required
       ↓
Todo::whereKey(10)
       ↓
Todo instance
       ↓
Controller
```

If the model doesn't exist, Laravel returns a 404 response.

---

# 19. Scoped Route Model Binding

Consider:

```text
/users/5/todos/10
```

You might want Todo `10` to belong to User `5`.

Laravel can support scoped bindings.

For example:

```php
Route::get('/users/{user}/todos/{todo}', ...)
    ->scopeBindings();
```

This helps ensure nested resources are resolved within their parent relationship.

Conceptually:

```text
User 5
   ↓
Todos belonging to User 5
   ↓
Find Todo 10
```

rather than simply finding Todo 10 globally.

This is particularly useful for authorization/resource ownership boundaries.

---

# 20. Resource Controllers ⭐⭐⭐

For CRUD applications, Laravel provides resource routing.

```php
Route::resource('todos', TodoController::class);
```

This generates conventional routes for:

| Method    | URI                  | Controller |
| --------- | -------------------- | ---------- |
| GET       | `/todos`             | index      |
| GET       | `/todos/create`      | create     |
| POST      | `/todos`             | store      |
| GET       | `/todos/{todo}`      | show       |
| GET       | `/todos/{todo}/edit` | edit       |
| PUT/PATCH | `/todos/{todo}`      | update     |
| DELETE    | `/todos/{todo}`      | destroy    |

This is extremely useful for CRUD applications.

---

# 21. Resource Controller

Generate one with Artisan:

```bash
php artisan make:controller TodoController --resource
```

You'll get methods such as:

```php
public function index()
{
}

public function create()
{
}

public function store(Request $request)
{
}

public function show(Todo $todo)
{
}

public function edit(Todo $todo)
{
}

public function update(Request $request, Todo $todo)
{
}

public function destroy(Todo $todo)
{
}
```

However, remember our architecture.

Don't put complicated business logic directly into:

```php
store()
update()
destroy()
```

Instead:

```text
Controller
    ↓
Form Request
    ↓
DTO
    ↓
Service
    ↓
Repository/Eloquent
```

---

# 22. API Resource Routes

For APIs, you often don't need HTML-oriented methods such as:

```text
create
edit
```

Laravel provides:

```php
Route::apiResource('todos', TodoController::class);
```

This focuses on API CRUD actions.

Conceptually:

```text
GET     /api/todos
POST    /api/todos
GET     /api/todos/{todo}
PUT     /api/todos/{todo}
PATCH   /api/todos/{todo}
DELETE  /api/todos/{todo}
```

---

# 23. Laravel Todo Architecture

Let's apply everything we've learned.

### routes/web.php

```php
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->group(function () {

        Route::resource('todos', TodoController::class);

    });
```

Architecture:

```text
Browser
   ↓
GET /todos/10
   ↓
Router
   ↓
auth middleware
   ↓
Route Model Binding
   ↓
TodoController@show
   ↓
TodoService
   ↓
Repository
   ↓
Database
```

That's much closer to production architecture.

---

# 24. Route Middleware + Model Binding

Consider:

```php
Route::middleware('auth')
    ->get('/todos/{todo}', [
        TodoController::class,
        'show',
    ]);
```

Controller:

```php
public function show(Todo $todo)
{
    return response()->json($todo);
}
```

The request pipeline is roughly:

```text
GET /todos/10
       ↓
Router
       ↓
Route matched
       ↓
auth middleware
       ↓
Route model binding
       ↓
TodoController@show
       ↓
Response
```

---

# 25. Raw PHP vs Laravel

In raw PHP you might write:

```php
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

if ($requestMethod === 'GET' && $requestUri === '/todos') {
    // Handle todos
}
```

As your application grows:

```text
if...
if...
if...
if...
```

becomes difficult to maintain.

Laravel gives you:

```php
Route::get('/todos', [
    TodoController::class,
    'index',
]);
```

The framework handles route matching, parameters, middleware integration, controller dispatching, and much more.

---

# 26. Route Listing

One of your best debugging tools:

```bash
php artisan route:list
```

You might see:

```text
GET|HEAD   /todos
POST       /todos
GET|HEAD   /todos/{todo}
PUT|PATCH  /todos/{todo}
DELETE     /todos/{todo}
```

This command is extremely useful when you're wondering:

> "Why isn't my route working?"

---

# 27. Route Cache

In production, Laravel can cache routes:

```bash
php artisan route:cache
```

Clear it with:

```bash
php artisan route:clear
```

Why?

Laravel doesn't have to rebuild the route definitions on every request in the same way.

For deployment, route caching can improve application startup/request performance.

### Important

Don't use route definitions that depend on runtime-changing data.

Routes should generally be statically defined application configuration.

---

# 28. Common Routing Mistakes ❌

## Mistake 1 — Business logic in routes

Bad:

```php
Route::post('/orders', function (Request $request) {

    // 100 lines of business logic

});
```

Better:

```php
Route::post('/orders', [
    OrderController::class,
    'store',
]);
```

Then:

```text
Controller
   ↓
OrderService
```

---

## Mistake 2 — Hardcoding URLs everywhere

Avoid:

```php
$url = '/todos/' . $todo->id;
```

Prefer named routes:

```php
$url = route('todos.show', $todo);
```

---

## Mistake 3 — No route constraints

If an ID must be numeric:

```php
Route::get('/todos/{id}')
    ->whereNumber('id');
```

Don't unnecessarily allow arbitrary values.

---

## Mistake 4 — Doing authorization only in the route

Authentication and authorization are different.

```text
Authentication:
"Who are you?"

Authorization:
"Are you allowed to do this?"
```

For example:

```text
User logged in?       → Authentication
Can edit Todo #10?    → Authorization
```

We'll later cover Policies and Gates.

---

# 29. Security Considerations 🔐

Routing itself is part of your application's security boundary.

### Authentication

Protect private routes:

```php
Route::middleware('auth')->group(function () {
    //
});
```

### Authorization

Don't assume:

```text
logged in = allowed
```

A user could be authenticated but still not own the resource.

For example:

```text
User 5
  ↓
requests
  ↓
/todos/100
  ↓
Todo 100 belongs to User 8
```

You need authorization.

### Route Model Binding

Binding can give you a clean 404 for missing resources, but **binding isn't authorization**.

---

# 30. Performance

For normal Laravel applications, routing isn't usually your main performance bottleneck.

More important problems are usually:

```text
Database queries
N+1 queries
External API calls
Large payloads
Poor caching
Expensive business logic
```

Still, production applications benefit from:

```bash
php artisan route:cache
```

and from keeping the route table clean and predictable.

---

# 31. When NOT to Use Resource Routes

Resource routes are excellent when your API/application follows CRUD conventions.

But don't force everything into CRUD.

For example:

```text
POST /orders/{order}/cancel
```

may be more expressive than trying to force:

```text
PUT /orders/{order}
```

with:

```json
{
  "status": "cancelled"
}
```

depending on your domain.

A business operation can deserve an explicit endpoint.

For example:

```text
POST /payments/{payment}/refund
POST /orders/{order}/cancel
POST /users/{user}/verify
```

The correct API design depends on the domain.

---

# 32. Interview Questions 🎯

### Q1. What is Laravel routing?

Routing maps an incoming HTTP request to the appropriate application handler.

---

### Q2. Difference between GET and POST?

```text
GET
→ retrieve/read

POST
→ create/submit
```

---

### Q3. What is route model binding?

Laravel automatically resolves route parameters into model instances.

```php
Route::get('/todos/{todo}', function (Todo $todo) {
    //
});
```

---

### Q4. What is a named route?

A route with a unique logical name:

```php
->name('todos.index')
```

which can be referenced using:

```php
route('todos.index')
```

---

### Q5. What is `Route::resource()`?

It generates conventional CRUD routes for a resource controller.

---

### Q6. Difference between `resource()` and `apiResource()`?

`resource()` includes web-oriented CRUD actions such as:

```text
create
edit
```

while `apiResource()` excludes those HTML form-oriented routes.

---

### Q7. Why use route constraints?

To restrict route parameters to expected formats.

```php
->whereNumber('id')
```

---

### Q8. Does route model binding perform authorization?

**No.**

It resolves the model.

Authorization is a separate concern.

---

# 33. Small Exercise 📝

Create routes for a Todo application.

Requirements:

```text
GET     /todos
GET     /todos/{todo}
POST    /todos
PUT     /todos/{todo}
DELETE  /todos/{todo}
```

Use:

```php
TodoController
```

and resource-style naming.

Your route names should be:

```text
todos.index
todos.show
todos.store
todos.update
todos.destroy
```

---

# 34. Challenge 🚀

Design this route:

```text
GET /users/{user}/todos/{todo}
```

Requirements:

1. Authenticate the user.
2. Use route model binding.
3. Use scoped bindings.
4. Call:

```php
TodoController@show
```

5. Ensure Todo belongs to the specified User.

Think about the architecture:

```text
GET /users/5/todos/10
          ↓
       Router
          ↓
    auth middleware
          ↓
    route model binding
          ↓
   User + Todo
          ↓
 TodoController@show
          ↓
     Authorization
          ↓
       Response
```

Try writing this route yourself before looking at the solution.

---

# 35. The Big Picture

You've now learned how Laravel moves from:

```text
URL
 ↓
Route
 ↓
Middleware
 ↓
Controller
 ↓
Service
 ↓
Repository
 ↓
Database
```

The most important routing concepts to remember are:

```text
Route::get()
Route::post()
Route::put()
Route::patch()
Route::delete()

Route parameters
Optional parameters
Route constraints

Named routes
Route groups
Middleware groups

Route Model Binding
Scoped bindings

Route::resource()
Route::apiResource()

route:list
route:cache
```

### Professional Laravel rule

Don't think:

> "How do I make this URL work?"

Think:

> **"What HTTP operation does this represent, who is allowed to perform it, and which application layer should handle the business logic?"**

That's the mindset that separates basic Laravel development from professional backend development.

---

## Next Lesson → Lesson 28: Laravel Middleware 🔐

We'll go deeply into:

```text
Request
   ↓
Middleware 1
   ↓
Middleware 2
   ↓
Middleware 3
   ↓
Controller
   ↓
Middleware 3
   ↓
Middleware 2
   ↓
Middleware 1
   ↓
Response
```

including **custom middleware, authentication middleware, middleware parameters, middleware groups, terminable middleware, ordering, security, and real-world authorization scenarios**.
