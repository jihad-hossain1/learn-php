# Lesson 29 — Laravel Controllers & Form Requests 🎯

You now understand:

```text
Route
   ↓
Middleware
   ↓
Controller
```

Today we'll make the controller layer **professional and maintainable**.

The key idea:

> **A controller should coordinate the request, not contain the application's business logic.**

We'll build toward this architecture:

```text
HTTP Request
     ↓
Route
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

---

# 1. What Is a Controller?

A controller is the application layer that receives a request and coordinates what should happen next.

Simple example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Todo list',
        ]);
    }
}
```

Route:

```php
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/todos', [TodoController::class, 'index']);
```

Request:

```text
GET /todos
```

Flow:

```text
GET /todos
    ↓
Router
    ↓
TodoController@index
    ↓
JSON Response
```

---

# 2. Real-World Analogy

Think of a restaurant.

```text
Customer
   ↓
Waiter
   ↓
Kitchen
   ↓
Chef
   ↓
Ingredients
```

The waiter doesn't cook the food.

The waiter:

- receives the order
- sends it to the kitchen
- brings the result back

A controller should work similarly.

```text
HTTP Request
     ↓
Controller
     ↓
Service
     ↓
Database
```

The controller shouldn't perform every operation itself.

---

# 3. The Fat Controller Problem ❌

Imagine this:

```php
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
    ]);

    $user = auth()->user();

    $todo = Todo::create([
        'title' => $request->title,
        'user_id' => $user->id,
    ]);

    Log::info('Todo created');

    Mail::to($user)->send(...);

    return response()->json($todo, 201);
}
```

It works.

But the controller is now responsible for:

```text
Validation
Authentication context
Business logic
Database
Logging
Email
Response
```

That's too much.

This is called a **fat controller**.

---

# 4. Thin Controller

Instead:

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

Now the controller mainly coordinates:

```text
Request
   ↓
Validate
   ↓
Create DTO
   ↓
Call Service
   ↓
Return Response
```

Much easier to understand.

---

# 5. Creating a Controller

Use Artisan:

```bash
php artisan make:controller TodoController
```

For a resource controller:

```bash
php artisan make:controller TodoController --resource
```

For an API resource controller:

```bash
php artisan make:controller TodoController --api
```

A resource controller typically contains:

```php
index()
create()
store()
show()
edit()
update()
destroy()
```

---

# 6. Controller Dependency Injection

This is where your previous **Service Container / Dependency Injection** lesson becomes important.

Suppose we have:

```php
final class TodoController extends Controller
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
needs TodoService
     ↓
Service Container
     ↓
resolve TodoService
     ↓
inject it
```

You don't need:

```php
$this->todoService = new TodoService();
```

Avoid manually constructing dependencies when Laravel can resolve them.

---

# 7. Why Constructor Injection?

Compare this:

### Less maintainable

```php
public function store()
{
    $service = new TodoService();

    $service->create();
}
```

with:

```php
public function __construct(
    private TodoService $todoService,
) {
}
```

Constructor injection makes the dependency explicit.

You can immediately see:

```text
TodoController
    requires
TodoService
```

It also makes testing easier.

---

# 8. Form Requests ⭐⭐⭐

Now we reach a very important Laravel feature.

Instead of putting validation inside the controller:

```php
$request->validate([
    'title' => ['required', 'string', 'max:255'],
]);
```

create a dedicated Form Request.

```bash
php artisan make:request StoreTodoRequest
```

You'll get:

```text
app/
└── Http/
    └── Requests/
        └── StoreTodoRequest.php
```

---

# 9. Form Request Structure

Example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

Now controller:

```php
public function store(
    StoreTodoRequest $request
): JsonResponse {
    //
}
```

Laravel automatically validates the request before your controller action continues.

---

# 10. Form Request Lifecycle

This is important.

Request:

```text
POST /todos
```

with:

```json
{
  "title": ""
}
```

Flow:

```text
Request
   ↓
Middleware
   ↓
Form Request
   ↓
Validation
   ↓
Fails?
 ┌──────┴──────┐
Yes           No
 ↓             ↓
Error       Controller
422             ↓
              Service
```

So your controller doesn't need:

```php
if ($request->fails()) {
    ...
}
```

Laravel handles the validation lifecycle.

---

# 11. Why `authorize()` Exists

A Form Request has:

```php
public function authorize(): bool
{
    return true;
}
```

This method answers:

> Is this user allowed to make this request?

For a simple public operation:

```php
public function authorize(): bool
{
    return true;
}
```

For a protected operation, authorization can be more meaningful.

For example, updating a Todo:

```php
public function authorize(): bool
{
    $todo = $this->route('todo');

    return $todo !== null
        && $this->user()?->can('update', $todo);
}
```

This connects Form Requests with Laravel authorization.

We'll study Policies deeply later.

---

# 12. Validation Rules

Example:

```php
public function rules(): array
{
    return [
        'title' => [
            'required',
            'string',
            'min:3',
            'max:255',
        ],

        'description' => [
            'nullable',
            'string',
        ],

        'priority' => [
            'required',
            'integer',
            'min:1',
            'max:5',
        ],
    ];
}
```

Now your validation rules have a dedicated home.

---

# 13. Common Validation Rules

Some useful rules:

```php
'required'
'nullable'
'string'
'integer'
'numeric'
'boolean'
'array'
'email'
'url'
'date'
'min:3'
'max:255'
```

Example:

```php
'email' => [
    'required',
    'email',
],
```

---

# 14. Validation With Database Rules

Suppose email must be unique:

```php
use Illuminate\Validation\Rule;

'email' => [
    'required',
    'email',
    Rule::unique('users', 'email'),
],
```

For updates, you often need to ignore the current user.

Laravel provides appropriate validation mechanisms for that situation.

Don't simply write custom SQL queries inside your controller to check uniqueness.

---

# 15. Enum Validation

You learned PHP Enums earlier.

Suppose:

```php
enum TodoPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
```

You can validate against the enum:

```php
use Illuminate\Validation\Rule;

'priority' => [
    'required',
    Rule::enum(TodoPriority::class),
],
```

This gives you a strong connection between:

```text
HTTP input
    ↓
Validation
    ↓
Domain enum
```

---

# 16. DTO + Form Request

Now combine what we learned earlier.

Form Request:

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
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
```

DTO:

```php
final readonly class CreateTodoData
{
    public function __construct(
        public string $title,
        public int $userId,
    ) {
    }

    public static function fromRequest(
        StoreTodoRequest $request
    ): self {
        return new self(
            title: $request->string('title')->toString(),
            userId: $request->user()->id,
        );
    }
}
```

Controller:

```php
public function store(
    StoreTodoRequest $request
): JsonResponse {
    $data = CreateTodoData::fromRequest($request);

    $todo = $this->todoService->create($data);

    return response()->json($todo, 201);
}
```

Now responsibilities are clearly separated.

---

# 17. Why Use a DTO?

Imagine your service accepts:

```php
public function create(CreateTodoData $data): Todo
```

It doesn't need to know about:

```text
HTTP Request
Laravel Request
headers
cookies
query strings
HTTP method
```

It receives application data:

```text
title
userId
```

This makes the service reusable.

For example:

```text
Web Controller
      ↓
     DTO
      ↓
   Service

API Controller
      ↓
     DTO
      ↓
   Service

CLI Command
      ↓
     DTO
      ↓
   Service

Queue Job
      ↓
     DTO
      ↓
   Service
```

That's a major architectural advantage.

---

# 18. Controller + Service Example

Service:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateTodoData;
use App\Models\Todo;

final class TodoService
{
    public function create(CreateTodoData $data): Todo
    {
        return Todo::create([
            'title' => $data->title,
            'user_id' => $data->userId,
        ]);
    }
}
```

Controller:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\CreateTodoData;
use App\Http\Requests\StoreTodoRequest;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;

final class TodoController extends Controller
{
    public function __construct(
        private TodoService $todoService,
    ) {
    }

    public function store(
        StoreTodoRequest $request
    ): JsonResponse {
        $todo = $this->todoService->create(
            CreateTodoData::fromRequest($request)
        );

        return response()->json($todo, 201);
    }
}
```

This is the architecture we want you to become comfortable with.

---

# 19. Request Data vs Validated Data

One common mistake is:

```php
$request->all()
```

and then passing everything into your application.

Avoid this.

If validation says:

```php
'title' => ['required', 'string'],
```

you should generally work with validated input.

For example:

```php
$data = $request->validated();
```

Then:

```php
$data['title']
```

contains validated input.

This reduces accidental processing of unexpected fields.

---

# 20. Mass Assignment Security

Suppose your request contains:

```json
{
  "title": "Learn Laravel",
  "user_id": 999,
  "is_admin": true
}
```

You don't want users to arbitrarily assign:

```text
user_id
is_admin
```

to themselves.

This is one reason you should avoid:

```php
Todo::create($request->all());
```

Instead explicitly control what enters your model:

```php
Todo::create([
    'title' => $data->title,
    'user_id' => $data->userId,
]);
```

The server determines:

```text
Who owns the Todo?
```

not the client.

---

# 21. Form Request Authorization

For an update request:

```php
final class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        return $todo !== null
            && $this->user()?->can('update', $todo);
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

This gives:

```text
Request
   ↓
Authentication
   ↓
Form Request
   ↓
Authorization
   ↓
Validation
   ↓
Controller
```

---

# 22. Validation Failure

Suppose:

```json
{
  "title": ""
}
```

and:

```php
'title' => ['required']
```

Validation fails.

For an API request, Laravel can return a validation error response, typically with HTTP status:

```text
422 Unprocessable Content
```

The controller does not execute normally.

Conceptually:

```text
POST /todos
      ↓
Validation
      ↓
FAIL
      ↓
422
```

This is another reason controllers stay clean.

---

# 23. Custom Validation Rule

Sometimes built-in rules aren't enough.

For example:

> Todo title cannot contain prohibited words.

You can create a custom rule:

```bash
php artisan make:rule ValidTodoTitle
```

Then use it:

```php
'title' => [
    'required',
    'string',
    new ValidTodoTitle(),
],
```

This is better than writing a giant validation condition inside your controller.

---

# 24. Controller Responsibilities

A good controller generally does things such as:

```text
Receive request
    ↓
Use validated input
    ↓
Call application/service layer
    ↓
Return HTTP response
```

It should generally NOT contain:

```text
Complex pricing algorithms
Payment processing
Large database workflows
Email implementation
Inventory calculations
Long authorization logic
```

Those belong elsewhere.

---

# 25. Controller Method Design

Avoid:

```php
public function store(Request $request)
{
    // 150 lines
}
```

Prefer:

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

A controller method should ideally be understandable at a glance.

---

# 26. Request Lifecycle — Full Picture

Let's connect everything you've learned.

Request:

```text
POST /todos
```

### 1. Web server

```text
Nginx / Apache
```

↓

### 2. Laravel bootstrap

```text
public/index.php
```

↓

### 3. Middleware

```text
auth
throttle
etc.
```

↓

### 4. Router

```text
POST /todos
```

↓

### 5. Form Request

```text
authorize()
rules()
```

↓

### 6. Controller

```text
TodoController@store
```

↓

### 7. DTO

```text
CreateTodoData
```

↓

### 8. Service

```text
TodoService
```

↓

### 9. Repository/Eloquent

```text
Todo::create()
```

↓

### 10. Database

```text
todos table
```

↓

### 11. Response

```json
{
  "id": 1,
  "title": "Learn Laravel"
}
```

That's the complete architecture you've been building toward.

---

# 27. Behind the Scenes

When Laravel sees:

```php
public function store(
    StoreTodoRequest $request
)
```

Laravel's service container and request lifecycle resolve the required dependencies.

Conceptually:

```text
Route
  ↓
Controller action
  ↓
Need StoreTodoRequest
  ↓
Laravel creates Form Request
  ↓
Authorization
  ↓
Validation
  ↓
Controller executes
```

You don't manually write:

```php
$request = new StoreTodoRequest(...);
```

Laravel handles the lifecycle.

---

# 28. API Response Design

Avoid returning random structures for every endpoint.

Bad:

```json
{
  "thing": "hello"
}
```

Another endpoint:

```json
{
  "result_data": "hello"
}
```

Another:

```json
{
  "response": {
    "value": "hello"
  }
}
```

Establish consistent API conventions.

Later, we'll study **Laravel API Resources**, which provide a dedicated way to transform models into API responses.

For now:

```php
return response()->json($todo, 201);
```

is fine for learning.

---

# 29. Common Mistakes ❌

### Mistake 1 — Fat controllers

```php
public function store()
{
    // 200 lines
}
```

Move business logic into services.

---

### Mistake 2 — Validation everywhere

Avoid repeating:

```php
$request->validate(...)
```

throughout controllers.

Use Form Requests when the validation is substantial/reusable.

---

### Mistake 3 — Passing raw request data to models

Avoid:

```php
Todo::create($request->all());
```

Explicitly map allowed application data.

---

### Mistake 4 — Letting client control ownership

Never trust:

```json
{
  "user_id": 500
}
```

to decide ownership.

Use:

```php
$request->user()->id
```

or your authenticated principal.

---

### Mistake 5 — Business logic in Form Requests

A Form Request is not a Service.

Avoid:

```php
public function rules()
{
    // calculate order total
    // charge payment
    // send email
}
```

Keep it focused on request authorization and validation.

---

# 30. Performance ⚡

Controllers themselves usually aren't your performance bottleneck.

The real problems are usually:

```text
N+1 queries
Huge database queries
External API calls
Large response payloads
Expensive computations
Missing indexes
```

However, good controller architecture helps you identify those problems because responsibilities are separated.

For example:

```text
Controller
   ↓
Service
   ↓
Repository
```

makes it much easier to locate expensive operations.

---

# 31. Security 🔐

Important principles:

### Validate input

```php
'title' => ['required', 'string', 'max:255']
```

### Authorize actions

```php
$this->user()->can(...)
```

### Don't trust IDs from clients

```text
user_id
owner_id
role
is_admin
```

should not blindly come from request input.

### Avoid mass assignment mistakes

Explicitly map fields.

### Don't expose sensitive model fields

Later, API Resources will help control response data.

---

# 32. When NOT to Use a Service

You don't need:

```text
Controller
 ↓
Service
 ↓
Repository
 ↓
Factory
 ↓
Manager
 ↓
Provider
```

for:

```php
public function health(): JsonResponse
{
    return response()->json([
        'status' => 'ok',
    ]);
}
```

That's overengineering.

Architecture should match complexity.

For a simple CRUD operation:

```text
Route
 ↓
Controller
 ↓
Eloquent
```

may be perfectly reasonable.

For complex business workflows:

```text
Route
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
Repository
 ↓
Eloquent
```

can be justified.

---

# 33. Interview Questions 🎯

### Q1. What is a controller?

A class responsible for coordinating an HTTP request and producing an appropriate response.

### Q2. What is a fat controller?

A controller containing excessive business logic and responsibilities.

### Q3. What is a Form Request?

A Laravel class used primarily for request authorization and validation.

### Q4. What is the purpose of `authorize()`?

To determine whether the current user is allowed to make that request.

### Q5. What is the purpose of `rules()`?

To define validation rules for incoming request data.

### Q6. Why use DTOs?

To transfer structured application data without coupling business logic to HTTP request objects.

### Q7. Why use dependency injection in controllers?

To make dependencies explicit, improve testability, and allow Laravel's container to manage object creation.

### Q8. Why shouldn't `$request->all()` be passed directly to `Model::create()`?

Because it can allow unintended request fields into persistence and increase mass-assignment/security risks.

---

# 34. Small Exercise 📝

Create a `StoreTodoRequest`.

Requirements:

```text
title:
    required
    string
    max 255

description:
    nullable
    string

priority:
    required
    integer
    min 1
    max 5
```

Then create:

```text
TodoController@store
```

that receives:

```php
StoreTodoRequest $request
```

Your controller should **not** contain validation rules.

---

# 35. Challenge 🚀

Build the complete creation flow.

### Route

```php
POST /todos
```

### Middleware

```text
auth
```

### Form Request

```text
StoreTodoRequest
```

### DTO

```text
CreateTodoData
```

### Service

```text
TodoService::create()
```

### Model

```text
Todo
```

Architecture:

```text
                 POST /todos
                      ↓
                    Route
                      ↓
                    auth
                      ↓
             StoreTodoRequest
               ↓           ↓
         authorize()     rules()
                      ↓
                TodoController
                      ↓
                CreateTodoData
                      ↓
                 TodoService
                      ↓
                    Todo
                      ↓
                  Database
                      ↓
                   JSON 201
```

### Extra requirement

The client sends:

```json
{
  "title": "Learn Laravel",
  "description": "Study controllers",
  "priority": 3
}
```

But **must not be allowed to choose**:

```json
{
  "user_id": 999
}
```

The authenticated user's ID must come from the server-side authentication context.

---

# 36. Final Mental Model

You have now learned four very important Laravel layers:

```text
┌─────────────────────────┐
│        ROUTING          │
│ Where does request go?  │
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│       MIDDLEWARE        │
│ Should request continue?│
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│     FORM REQUEST        │
│ Is input valid/allowed? │
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│       CONTROLLER        │
│ Coordinate the request  │
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│         DTO             │
│ Transfer clean data     │
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│        SERVICE          │
│ Business logic          │
└────────────┬────────────┘
             ↓
┌─────────────────────────┐
│    REPOSITORY/ELOQUENT  │
│ Data persistence        │
└─────────────────────────┘
```

### The professional rule to remember

> **Validate at the boundary, authorize explicitly, keep controllers thin, keep business logic out of HTTP concerns, and pass clean data into your application layer.**

---

## Next Lesson → Lesson 30: Laravel Blade & Views 🎨

We'll move into Laravel's presentation layer and learn:

- Blade syntax
- layouts
- components
- slots
- sections
- loops and conditions
- forms
- CSRF
- escaping/XSS protection
- Blade directives
- reusable components
- view composers
- passing data from Controller → View
- Blade vs API responses
- common Blade security mistakes
- building the Todo UI

After that, we'll continue deeper into Laravel's **service container, service providers, configuration, database, Eloquent, relationships, authentication, APIs, queues, caching, testing, and production architecture**.
