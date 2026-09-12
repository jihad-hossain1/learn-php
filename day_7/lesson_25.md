# Lesson 25 — Laravel Service Layer, DTOs & Repository Architecture

Today we're going to turn the concepts from the previous lessons into a **real Laravel application architecture**.

This is an important milestone because you'll start thinking like a backend engineer rather than simply writing controller code.

---

# 1. The Problem With "Everything in the Controller"

A beginner Laravel application often looks like this:

```php
public function store(Request $request)
{
    $request->validate([
        'title' => ['required', 'string'],
    ]);

    $todo = Todo::create([
        'title' => $request->title,
        'user_id' => auth()->id(),
    ]);

    return response()->json($todo);
}
```

This isn't necessarily wrong.

For a tiny application, it's perfectly reasonable.

But imagine the application grows.

Now creating a Todo requires:

```text
Validate input
    ↓
Check permissions
    ↓
Create Todo
    ↓
Write activity log
    ↓
Send notification
    ↓
Update statistics
    ↓
Dispatch event
```

Putting all of that into the controller creates a **fat controller**.

---

# 2. Professional Architecture

Instead, we can separate responsibilities:

```text
HTTP Request
     ↓
Form Request
     ↓
Controller
     ↓
Service
     ↓
DTO
     ↓
Repository
     ↓
Eloquent
     ↓
Database
```

Each layer has a purpose.

| Layer        | Responsibility                |
| ------------ | ----------------------------- |
| Form Request | Validate/authorize HTTP input |
| Controller   | HTTP coordination             |
| DTO          | Carry structured data         |
| Service      | Business logic                |
| Repository   | Data access                   |
| Model        | Database/entity behavior      |
| Database     | Persist data                  |

---

# 3. Real-World Analogy

Think about a restaurant.

```text
Customer
   ↓
Waiter
   ↓
Order ticket
   ↓
Kitchen manager
   ↓
Chef
   ↓
Ingredients
```

The waiter shouldn't cook the food.

Likewise:

```text
Controller
```

shouldn't perform every piece of business logic.

---

# 4. Controller = Coordinator

A good controller should look boring.

That's actually a **good thing**.

Example:

```php
final class TodoController
{
    public function __construct(
        private TodoService $todoService
    ) {
    }

    public function store(
        StoreTodoRequest $request
    ): JsonResponse {
        $todo = $this->todoService->create(
            $request->validated()
        );

        return response()->json($todo, 201);
    }
}
```

The controller basically says:

> "Request is valid. Service, please create the Todo."

That's it.

---

# 5. Form Request

Instead of:

```php
$request->validate([
    'title' => ['required', 'string', 'max:255'],
]);
```

inside every controller method, use a Form Request.

```php
final class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
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

Now the controller receives validated input.

---

# 6. Why Form Request?

It separates:

```text
HTTP validation
```

from:

```text
Business logic
```

That's important.

Validation answers:

> "Is this request structurally valid?"

Business logic answers:

> "What should the application do with this valid request?"

Those are different responsibilities.

---

# 7. DTO — Data Transfer Object

Now we introduce another important concept.

A DTO is an object used to carry structured data between parts of your application.

Instead of:

```php
$this->todoService->create(
    $request->validated()
);
```

you can convert the data into a specific object.

For example:

```php
final readonly class CreateTodoData
{
    public function __construct(
        public string $title,
        public int $userId,
    ) {
    }
}
```

Notice:

```php
readonly
```

This means the properties cannot be reassigned after construction.

That's useful for data objects.

---

# 8. Why DTO Instead of Arrays?

An array is flexible:

```php
$data['title']
$data['user_id']
```

But flexibility can create mistakes.

For example:

```php
$data['titel']
```

PHP won't necessarily tell you that you made a typo until runtime.

With a DTO:

```php
$data->title
```

you get a clearly defined structure.

You know exactly what the service expects.

---

# 9. DTO Flow

```text
HTTP Request
      ↓
Validated Array
      ↓
CreateTodoData
      ↓
TodoService
```

The DTO becomes a contract for the data.

---

# 10. Creating the DTO

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

Then your controller can do:

```php
public function store(
    StoreTodoRequest $request
): JsonResponse {
    $data = CreateTodoData::fromRequest($request);

    $todo = $this->todoService->create($data);

    return response()->json($todo, 201);
}
```

---

# 11. Service Layer

Now the business logic goes into the service.

```php
final class TodoService
{
    public function __construct(
        private TodoRepository $todoRepository
    ) {
    }

    public function create(
        CreateTodoData $data
    ): Todo {
        return $this->todoRepository->create(
            title: $data->title,
            userId: $data->userId,
        );
    }
}
```

Now imagine the business rule becomes:

```text
Create Todo
   ↓
Create database record
   ↓
Log activity
   ↓
Send notification
```

That logic belongs naturally in the Service.

---

# 12. Repository

The Repository handles data access.

Interface:

```php
interface TodoRepository
{
    public function create(
        string $title,
        int $userId
    ): Todo;
}
```

Implementation:

```php
final class EloquentTodoRepository implements TodoRepository
{
    public function create(
        string $title,
        int $userId
    ): Todo {
        return Todo::query()->create([
            'title' => $title,
            'user_id' => $userId,
        ]);
    }
}
```

Now:

```text
TodoService
     ↓
TodoRepository
     ↓
EloquentTodoRepository
     ↓
Todo Model
```

---

# 13. Binding the Repository

Laravel's container needs to know:

```text
TodoRepository
       ↓
EloquentTodoRepository
```

You can bind it:

```php
$this->app->bind(
    TodoRepository::class,
    EloquentTodoRepository::class
);
```

Now when Laravel needs:

```php
TodoRepository
```

it knows which implementation to resolve.

---

# 14. Complete Architecture

We now have:

```text
┌─────────────────────┐
│    HTTP Request     │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│  StoreTodoRequest   │
│ Validation/Auth     │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│   TodoController    │
│ HTTP coordination   │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│    CreateTodoData   │
│        DTO          │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│     TodoService     │
│   Business Logic    │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│   TodoRepository    │
│    Data Access      │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│ EloquentTodoRepo    │
└──────────┬──────────┘
           ↓
┌─────────────────────┐
│     Todo Model      │
└──────────┬──────────┘
           ↓
       Database
```

This is a **layered architecture**.

---

# 15. But Is This Always Necessary?

No.

This is extremely important.

For a simple CRUD operation:

```php
Todo::create([...]);
```

may be completely fine.

Don't create:

```text
Controller
Service
DTO
Repository
Repository Interface
Factory
Strategy
Adapter
```

just to insert one row.

That's overengineering.

---

# 16. When Should You Introduce a Service?

A Service becomes valuable when you have **business logic**.

For example:

```text
Create Order
    ↓
Validate inventory
    ↓
Calculate discounts
    ↓
Create order
    ↓
Reserve inventory
    ↓
Charge payment
    ↓
Send notification
```

That's clearly more than simple CRUD.

A service is appropriate:

```php
$orderService->create($data);
```

---

# 17. When Should You Introduce a Repository?

Use a Repository when data-access logic becomes meaningful.

For example:

```php
public function findAvailableRooms(
    Carbon $checkIn,
    Carbon $checkOut,
    int $guests
): Collection
```

That's a meaningful data-access abstraction.

But this:

```php
public function find(int $id): ?Todo
{
    return Todo::find($id);
}
```

may not justify a repository by itself.

---

# 18. Service vs Repository

This distinction is critical.

### Service

Answers:

> **What should the application do?**

Example:

```text
Create booking
Check room
Calculate price
Charge payment
Send confirmation
```

### Repository

Answers:

> **How do we retrieve/store the data?**

Example:

```text
Find booking
Find available rooms
Save booking
Get user's bookings
```

Think:

```text
Service = Business decisions

Repository = Data access
```

---

# 19. Don't Put Business Logic in Repository

Bad:

```php
final class OrderRepository
{
    public function createOrder(...): Order
    {
        // calculate discount
        // charge payment
        // send email
        // create order
    }
}
```

That's too much responsibility.

Better:

```text
OrderService
   │
   ├── DiscountService
   ├── PaymentGateway
   ├── InventoryService
   └── OrderRepository
```

The Repository should remain focused on persistence/querying.

---

# 20. Transactions

Now we reach an important real-world situation.

Suppose creating an order requires:

```text
1. Create order
2. Create order items
3. Reduce inventory
4. Record payment
```

What happens if step 4 fails?

You don't want:

```text
Order created ✅
Items created ✅
Inventory reduced ✅
Payment failed ❌
```

Now your data is inconsistent.

Use a database transaction.

```php
DB::transaction(function () use ($data) {
    // Create order
    // Create items
    // Update inventory
});
```

If an exception occurs:

```text
Rollback
   ↓
Database returns to previous state
```

---

# 21. Service + Transaction

A realistic service might look like:

```php
final class OrderService
{
    public function __construct(
        private OrderRepository $orders
    ) {
    }

    public function create(
        CreateOrderData $data
    ): Order {
        return DB::transaction(
            function () use ($data): Order {
                $order = $this->orders->create(
                    $data
                );

                // Additional business operations...

                return $order;
            }
        );
    }
}
```

This is much closer to production code.

---

# 22. Security

The architecture also helps security.

### Form Request

Controls:

```text
Authentication
Authorization
Input validation
```

### Service

Controls:

```text
Business rules
```

### Repository

Controls:

```text
Data access
```

But never assume architecture automatically prevents security problems.

For example, don't trust:

```php
$userId = $request->input('user_id');
```

just because it's validated.

You must also determine:

> Is this user actually allowed to create a Todo for that user ID?

That's an **authorization** question.

---

# 23. Performance

Layering has a small abstraction cost.

But your real performance problems usually come from:

```text
❌ N+1 queries
❌ Missing indexes
❌ Huge datasets
❌ Slow external APIs
❌ Unnecessary database queries
❌ Loading unnecessary columns
```

Don't remove a useful service layer because you're worried about the cost of one method call.

Focus optimization where the actual bottleneck is.

---

# 24. Common Mistakes

### ❌ Fat Controller

```php
public function store()
{
    // 300 lines
}
```

Move business logic to services.

---

### ❌ Fat Service

Don't create:

```text
ApplicationService.php
```

with 5,000 lines.

Split responsibilities.

---

### ❌ Anemic Repository

Don't create repositories that only wrap every single Eloquent call without adding value.

---

### ❌ DTO Everywhere

DTOs are useful when they clarify data contracts.

For trivial operations, arrays can be perfectly acceptable.

---

### ❌ Service Calling HTTP Request Directly

Avoid:

```php
public function create(Request $request)
```

inside a service.

Your service shouldn't depend on the HTTP layer.

Prefer:

```php
public function create(CreateTodoData $data)
```

This allows the service to be used from:

```text
Controller
Job
Command
Event Listener
API
Test
```

---

# 25. Why This Architecture Is Powerful

Suppose tomorrow you want to create Todos from:

```text
Web
API
CLI command
Queue job
```

If your business logic is inside the controller:

```text
Web Controller
     ↓
Business Logic
```

the API and CLI may duplicate it.

Instead:

```text
Web Controller ──┐
API Controller ──┼──→ TodoService
CLI Command ─────┤
Queue Job ───────┘
```

Now business logic exists in one place.

That's a major reason for the Service Layer.

---

# 26. Interview Questions

### Q1. What should a Controller do?

Coordinate HTTP input/output and delegate business operations.

### Q2. What is a DTO?

An object designed to transfer structured data between application layers.

### Q3. What belongs in a Service?

Business/application logic and orchestration.

### Q4. What belongs in a Repository?

Data-access and persistence/query logic.

### Q5. Should services depend directly on `Request`?

Generally no. Keep business/application logic independent from HTTP concerns.

### Q6. Should every CRUD operation have a Service?

No. Use services when they provide meaningful separation or business logic.

### Q7. Should every model have a Repository?

No.

### Q8. Why use transactions?

To keep related database changes atomic and prevent partially completed operations.

---

# 27. Small Exercise 📝

Build a Todo creation flow with these components:

```text
StoreTodoRequest
CreateTodoData
TodoController
TodoService
TodoRepository
EloquentTodoRepository
Todo
```

Flow:

```text
POST /todos
    ↓
StoreTodoRequest
    ↓
CreateTodoData
    ↓
TodoController
    ↓
TodoService
    ↓
TodoRepository
    ↓
Todo
```

Requirements:

### Validation

```text
title: required|string|max:255
```

### DTO

```php
final readonly class CreateTodoData
{
    public function __construct(
        public string $title,
        public int $userId,
    ) {
    }
}
```

### Repository

It should create the Todo.

### Service

It should orchestrate the creation.

### Controller

It should remain very thin.

---

# 28. Challenge 🔥 — Todo Completion

Now extend the architecture.

When a user completes a Todo:

```text
Request
   ↓
Form Request
   ↓
Controller
   ↓
TodoService
   ↓
Repository
   ↓
Database
```

Business rules:

1. Todo must exist.
2. Todo must belong to the authenticated user.
3. Todo must not already be completed.
4. Mark it completed.
5. Return the updated Todo.

Think carefully about **where each rule belongs**.

For example:

```text
"Is the request valid?"
        ↓
Form Request

"Is the user allowed?"
        ↓
Authorization / Policy

"Can this Todo transition to completed?"
        ↓
Business logic / Service

"How do we update it?"
        ↓
Repository / Eloquent
```

This separation is exactly the kind of thinking you need as a professional Laravel developer.

---

# 29. Summary

You've now learned an important architectural structure:

```text
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
     ↓
Database
```

Remember these four questions:

> **Form Request:** Is the HTTP input valid and authorized?

> **Controller:** What HTTP operation is being requested?

> **Service:** What should the application do?

> **Repository:** How should the data be accessed?

And one final professional rule:

> **Don't add architecture because it looks professional. Add it because it solves a real complexity problem.**

---

## Next Lesson → Laravel Project Structure & Request Lifecycle

We'll now step into **Laravel Core** and understand what actually happens from:

```text
Browser
   ↓
public/index.php
   ↓
Laravel Bootstrap
   ↓
Service Container
   ↓
Middleware
   ↓
Router
   ↓
Controller
   ↓
Response
   ↓
Browser
```

Understanding this lifecycle will make Laravel's **middleware, service providers, container, routing, controllers, and dependency injection** much easier to understand.
