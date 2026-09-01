# 🚀 Module 2 — Lesson 11: OOP Part 1 — Classes & Objects

Now we're entering one of the **most important parts of PHP development**.

Laravel is heavily based on **Object-Oriented Programming (OOP)**.

If you understand OOP well, Laravel becomes much easier to understand.

Today we'll learn:

- Class
- Object
- Properties
- Methods
- `$this`
- Constructor
- Visibility
- Why OOP exists
- Procedural PHP → OOP
- Refactoring part of our Todo app

---

# 1. Why Do We Need OOP?

Our Todo application currently has:

```php
$tasks = [];

addTask($tasks, $title);
completeTask($tasks, $id);
deleteTask($tasks, $id);
listTasks($tasks);
```

Notice the problem?

Every function needs:

```php
$tasks
```

As the application grows, we might have:

```text
addTask($tasks)
deleteTask($tasks)
updateTask($tasks)
completeTask($tasks)
findTask($tasks)
searchTask($tasks)
countTasks($tasks)
clearCompleted($tasks)
```

That's difficult to manage.

OOP lets us put **data and the operations that work with that data together**.

Instead:

```php
$todoList->add();
$todoList->delete();
$todoList->complete();
```

The object owns the data.

---

# 2. Real-World Analogy

Think about a car.

A car has **data**:

```text
Brand
Model
Color
Speed
```

And it has **behavior**:

```text
start()
stop()
accelerate()
brake()
```

In OOP:

```text
             Car
              │
       ┌──────┴──────┐
       │             │
    Properties     Methods
       │             │
   brand           start()
   model           stop()
   color           accelerate()
   speed           brake()
```

That's the basic idea behind classes.

---

# 3. What Is a Class?

A class is a **blueprint**.

```php
class Car
{
}
```

This doesn't create a car yet.

It defines what a `Car` should look like.

Think:

```text
Class = Blueprint
Object = Actual thing created from blueprint
```

---

# 4. Creating an Object

Use `new`:

```php
$car = new Car();
```

Now:

```text
Car class
   ↓
new Car()
   ↓
$car object
```

You can create multiple objects:

```php
$car1 = new Car();
$car2 = new Car();
$car3 = new Car();
```

All three are objects created from the same class.

---

# 5. Properties

A property represents data belonging to an object.

```php
class Car
{
    public string $brand;
    public string $model;
}
```

Create the object:

```php
$car = new Car();

$car->brand = 'Toyota';
$car->model = 'Corolla';
```

Access the properties:

```php
echo $car->brand;
echo $car->model;
```

Output:

```text
Toyota
Corolla
```

Notice:

```php
$car->brand
```

not:

```php
$car['brand']
```

Arrays use:

```php
$array['key']
```

Objects use:

```php
$object->property
```

---

# 6. Methods

A method is a function inside a class.

```php
class Car
{
    public function start(): void
    {
        echo 'Car started.';
    }
}
```

Use it:

```php
$car = new Car();

$car->start();
```

Output:

```text
Car started.
```

So:

```text
Function outside class → Function

Function inside class → Method
```

---

# 7. `$this`

This is one of the most important concepts.

Inside a class:

```php
$this
```

means:

> The current object.

Example:

```php
class Car
{
    public string $brand;

    public function showBrand(): void
    {
        echo $this->brand;
    }
}
```

Then:

```php
$car = new Car();

$car->brand = 'Toyota';

$car->showBrand();
```

Output:

```text
Toyota
```

Here:

```php
$this->brand
```

means:

> Get the `brand` property from this particular object.

---

# 8. Multiple Objects

This makes `$this` easier to understand.

```php
class Car
{
    public string $brand;

    public function showBrand(): void
    {
        echo $this->brand . PHP_EOL;
    }
}
```

Now:

```php
$car1 = new Car();
$car1->brand = 'Toyota';

$car2 = new Car();
$car2->brand = 'Honda';

$car1->showBrand();
$car2->showBrand();
```

Output:

```text
Toyota
Honda
```

The same method works with different object data.

```text
$car1
 └── brand = Toyota
       ↓
 $this = $car1


$car2
 └── brand = Honda
       ↓
 $this = $car2
```

---

# 9. Constructor

A constructor runs automatically when an object is created.

```php
class User
{
    public function __construct()
    {
        echo 'User created.';
    }
}
```

Now:

```php
$user = new User();
```

Output:

```text
User created.
```

You don't call `__construct()` manually.

PHP automatically calls it when:

```php
new User()
```

runs.

---

# 10. Constructor Parameters

This is much more useful.

```php
class User
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

Now:

```php
$user = new User('Alice');

echo $user->name;
```

Output:

```text
Alice
```

Conceptually:

```text
new User('Alice')
       ↓
__construct('Alice')
       ↓
$this->name = 'Alice'
```

---

# 11. Constructor Property Promotion

Modern PHP gives us a cleaner syntax.

Instead of:

```php
class User
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

PHP 8+ allows:

```php
class User
{
    public function __construct(
        public string $name,
    ) {
    }
}
```

This is called **constructor property promotion**.

You'll see this constantly in modern Laravel applications.

---

# 12. Visibility

Properties and methods can have visibility.

There are three main levels:

```text
public
protected
private
```

---

## `public`

Accessible from anywhere.

```php
class User
{
    public string $name;
}
```

You can:

```php
$user->name = 'Alice';
```

---

## `private`

Only accessible inside the class itself.

```php
class User
{
    private string $password;

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
```

This won't work:

```php
$user->password;
```

because `$password` is private.

---

## `protected`

Accessible inside the class and child classes.

We'll study inheritance later.

For now:

```text
public
   ↓
Everywhere

protected
   ↓
Class + child classes

private
   ↓
Only the class itself
```

---

# 13. Why Use `private`?

Consider a bank account.

Bad design:

```php
class BankAccount
{
    public float $balance;
}
```

Anyone can do:

```php
$account->balance = -1000000;
```

That's dangerous.

Better:

```php
class BankAccount
{
    private float $balance = 0;
}
```

Then expose controlled behavior:

```php
class BankAccount
{
    private float $balance = 0;

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException(
                'Amount must be positive.'
            );
        }

        $this->balance += $amount;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}
```

Now:

```php
$account = new BankAccount();

$account->deposit(100);

echo $account->getBalance();
```

Output:

```text
100
```

This is **encapsulation**.

---

# 14. Encapsulation

Encapsulation means:

> Keep an object's internal state controlled and expose a clear public interface.

Think:

```text
             BankAccount
                  │
       ┌──────────┴──────────┐
       │                     │
   private balance       public methods
                             │
                       deposit()
                       getBalance()
```

Users of the class don't need to know exactly how the balance is stored.

They simply use:

```php
$account->deposit(100);
```

This becomes very important in professional software.

---

# 15. Refactoring Our Todo Application

Remember our procedural version:

```php
$tasks = [];

addTask($tasks, $title);
deleteTask($tasks, $id);
completeTask($tasks, $id);
```

Let's turn it into an object.

First:

```php
class TodoList
{
    private array $tasks = [];
}
```

Now the tasks belong to the object.

---

# 16. Add a Method

```php
class TodoList
{
    private array $tasks = [];

    public function addTask(string $title): void
    {
        $this->tasks[] = [
            'id' => count($this->tasks) + 1,
            'title' => $title,
            'completed' => false,
        ];
    }
}
```

Create the object:

```php
$todoList = new TodoList();
```

Add a task:

```php
$todoList->addTask('Learn PHP');
$todoList->addTask('Learn Laravel');
```

Notice what disappeared:

```text
$tasks
```

We don't need to pass it around anymore.

The object owns it.

---

# 17. Add `listTasks()`

```php
class TodoList
{
    private array $tasks = [];

    public function addTask(string $title): void
    {
        $this->tasks[] = [
            'id' => count($this->tasks) + 1,
            'title' => $title,
            'completed' => false,
        ];
    }

    public function listTasks(): void
    {
        foreach ($this->tasks as $task) {
            $status = $task['completed'] ? '✓' : ' ';

            echo "[{$status}] {$task['id']}. {$task['title']}" . PHP_EOL;
        }
    }
}
```

Now:

```php
$todoList = new TodoList();

$todoList->addTask('Learn PHP');
$todoList->addTask('Learn Laravel');

$todoList->listTasks();
```

Output:

```text
[ ] 1. Learn PHP
[ ] 2. Learn Laravel
```

Much cleaner.

---

# 18. Complete Task

```php
public function completeTask(int $taskId): bool
{
    foreach ($this->tasks as &$task) {
        if ($task['id'] === $taskId) {
            $task['completed'] = true;

            unset($task);

            return true;
        }
    }

    unset($task);

    return false;
}
```

Now:

```php
$todoList->completeTask(1);
```

Then:

```php
$todoList->listTasks();
```

Output:

```text
[✓] 1. Learn PHP
[ ] 2. Learn Laravel
```

---

# 19. Our New Architecture

Before:

```text
$tasks
  │
  ├── addTask()
  ├── listTasks()
  ├── completeTask()
  └── deleteTask()
```

After:

```text
             TodoList
                │
         private $tasks
                │
      ┌─────────┼──────────┐
      ↓         ↓          ↓
   addTask() listTasks() completeTask()
```

This is the fundamental benefit of OOP.

---

# 20. Raw PHP vs Laravel

In raw PHP:

```php
class User
{
    public function getName(): string
    {
        return 'Alice';
    }
}
```

In Laravel, you'll see classes everywhere:

```php
class UserController extends Controller
{
    public function index(): View
    {
        // ...
    }
}
```

Services:

```php
class OrderService
{
    public function createOrder(): Order
    {
        // ...
    }
}
```

Models:

```php
class User extends Model
{
    // ...
}
```

Requests:

```php
class StoreUserRequest extends FormRequest
{
    // ...
}
```

Laravel isn't replacing OOP.

**Laravel is built on OOP.**

---

# 21. Behind the Scenes

When you write:

```php
$todoList = new TodoList();
```

PHP:

1. Finds the `TodoList` class.
2. Allocates an object.
3. Initializes its properties.
4. Runs `__construct()` if one exists.
5. Returns the object.
6. Stores the object reference in `$todoList`.

Then:

```php
$todoList->addTask('Learn PHP');
```

PHP calls the `addTask()` method on that specific object.

---

# 22. Common Mistakes

### Mistake 1 — Forgetting `$this`

Inside a class:

```php
$name = 'Alice';

public function showName(): void
{
    echo $name;
}
```

Wrong.

Use:

```php
echo $this->name;
```

when `$name` is an object property.

---

### Mistake 2 — Using `->` with arrays

Wrong:

```php
$user->name;
```

if `$user` is an array.

Use:

```php
$user['name'];
```

Objects:

```php
$user->name;
```

---

### Mistake 3 — Making everything public

Avoid:

```php
public array $tasks;
```

when outside code shouldn't directly modify it.

Prefer:

```php
private array $tasks = [];
```

and expose methods.

---

# 23. Performance

Creating objects has some overhead compared with simple arrays.

But don't choose procedural code everywhere just because objects have overhead.

For real applications, maintainability and correctness are usually far more important.

Laravel applications rely heavily on objects because they provide structure and separation of responsibilities.

---

# 24. Security

Encapsulation can help protect sensitive state.

For example:

```php
private string $passwordHash;
```

is better than exposing sensitive internal state publicly.

But remember:

> `private` is not encryption.

It controls access within PHP code; it doesn't protect data stored in a database.

---

# 🎯 Practice Exercise

Create a `Product` class.

It should have:

```text
name
price
```

Example:

```php
$product = new Product('Laptop', 800.00);
```

Add a method:

```php
$product->getDetails();
```

Expected:

```text
Laptop - $800
```

Use:

- Constructor
- Constructor property promotion
- Proper types
- `private` properties

---

# 🔥 Challenge

Create a `BankAccount` class.

Requirements:

```text
private balance
```

Methods:

```text
deposit()
withdraw()
getBalance()
```

Rules:

- Deposit must be greater than `0`.
- Withdrawal must be greater than `0`.
- Cannot withdraw more than the balance.
- Return the updated balance.
- Throw an exception for invalid operations.

Example:

```php
$account = new BankAccount();

$account->deposit(1000);
$account->withdraw(250);

echo $account->getBalance();
```

Expected:

```text
750
```

---

# 🧠 Interview Questions

Try answering these:

1. What is a class?
2. What is an object?
3. What's the difference between a class and an object?
4. What does `$this` mean?
5. What is a property?
6. What is a method?
7. What does `__construct()` do?
8. What's the difference between `public`, `protected`, and `private`?
9. What is encapsulation?
10. Why shouldn't every property be public?
11. What does `new` do?
12. What's the difference between `$user['name']` and `$user->name`?

---

# 📌 Today's Mental Model

Remember this:

```text
CLASS
  │
  │ new
  ▼
OBJECT
  │
  ├── Properties → Data
  │
  └── Methods    → Behavior
```

And:

```text
$this
  ↓
Current object
```

---

# Next Lesson — OOP Part 2

Next we'll learn:

- **Inheritance**
- `extends`
- Parent and child classes
- Method overriding
- `parent::`
- `protected`
- Abstract classes
- When inheritance is useful
- Why **composition is often better than inheritance**
- Laravel examples

Then we'll move into **Interfaces**, which are extremely important for professional Laravel development and dependency injection.
