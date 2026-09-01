# 🚀 Module 2 — Lesson 15: Namespaces, `use` & Composer Autoloading

Today we're learning something you'll use **every day in Laravel**.

By the end, you'll understand:

```text
namespace
use
fully qualified class names
PSR-4
Composer autoloading
vendor/autoload.php
```

The big picture:

```text
PHP Classes
    ↓
Namespaces
    ↓
Composer
    ↓
PSR-4 Autoloading
    ↓
Laravel
```

---

# 1. Why Do We Need Namespaces?

Imagine you have two classes both named:

```php
User
```

For example:

```text
Admin/User.php
Customer/User.php
```

Both are called `User`.

Without namespaces, PHP can't distinguish them properly.

Namespaces solve this.

```text
App\Admin\User
App\Customer\User
```

Now PHP knows they are different classes.

---

# 2. Basic Namespace

Suppose we have:

```text
app/
└── Services/
    └── PaymentService.php
```

Inside the file:

```php
<?php

declare(strict_types=1);

namespace App\Services;

class PaymentService
{
    public function pay(): void
    {
        echo 'Payment processed.';
    }
}
```

The important line is:

```php
namespace App\Services;
```

This means the class's full name is:

```text
App\Services\PaymentService
```

---

# 3. Namespace Is Like a Folder

For learning purposes, think of it like this:

```text
App
└── Services
    └── PaymentService
```

Namespace:

```php
namespace App\Services;
```

Class:

```php
class PaymentService
```

Together:

```text
App\Services\PaymentService
```

⚠️ But remember:

> A namespace is not literally a filesystem folder.

They are related through Composer's autoloading configuration, especially PSR-4.

We'll get there.

---

# 4. Using a Namespaced Class

Suppose:

```php
namespace App\Services;

class PaymentService
{
}
```

Another file wants to use it.

You can write:

```php
use App\Services\PaymentService;
```

Then:

```php
$paymentService = new PaymentService();
```

Complete example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PaymentService;

class PaymentController
{
    public function store(): void
    {
        $paymentService = new PaymentService();

        $paymentService->pay();
    }
}
```

---

# 5. What Does `use` Actually Do?

This is important.

When you write:

```php
use App\Services\PaymentService;
```

you're basically telling PHP:

> When I write `PaymentService` in this file, I mean `App\Services\PaymentService`.

So:

```php
new PaymentService();
```

is shorthand for:

```php
new \App\Services\PaymentService();
```

---

# 6. Fully Qualified Class Name

You can use the full name directly:

```php
$service = new \App\Services\PaymentService();
```

Notice the leading:

```text
\
```

That means start from the global namespace.

Usually this is cleaner:

```php
use App\Services\PaymentService;

$service = new PaymentService();
```

This is why Laravel files contain many `use` statements.

---

# 7. Multiple Classes

Suppose you have:

```text
App\Models\User
App\Models\Order
App\Services\OrderService
App\Services\PaymentService
```

Your controller might look like:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PaymentService;

class OrderController
{
    // ...
}
```

You'll see this pattern constantly in Laravel.

---

# 8. Namespace Conflicts

Suppose:

```text
App\Admin\User
App\Customer\User
```

You can't simply do:

```php
use App\Admin\User;
use App\Customer\User;
```

because both have the same short name:

```text
User
```

You can use an alias.

```php
use App\Admin\User as AdminUser;
use App\Customer\User as CustomerUser;
```

Now:

```php
$admin = new AdminUser();

$customer = new CustomerUser();
```

Very useful when two classes have the same name.

---

# 9. Real-World Laravel Example

Imagine:

```text
app/
├── Models/
│   └── Order.php
│
├── Services/
│   └── OrderService.php
│
└── Http/
    └── Controllers/
        └── OrderController.php
```

`Order.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

class Order
{
}
```

`OrderService.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

final class OrderService
{
    public function create(): Order
    {
        return new Order();
    }
}
```

`OrderController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrderService;

final class OrderController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }

    public function store(): void
    {
        $order = $this->orderService->create();

        // ...
    }
}
```

Notice how every class has a namespace and imports the classes it needs.

---

# 10. What Problem Does Composer Solve?

Now here's the next question:

If `OrderService` uses:

```php
use App\Models\Order;
```

how does PHP know where `Order.php` actually lives?

Do we need:

```php
require_once 'app/Models/Order.php';
```

everywhere?

Imagine doing that in a Laravel application with hundreds or thousands of classes.

Terrible.

Composer solves this using **autoloading**.

---

# 11. Composer

Composer is PHP's dependency manager.

You can install packages such as:

```text
Laravel
Guzzle
PHPUnit
Symfony components
Monolog
```

But Composer does another very important job:

> It can automatically load PHP classes.

That's **autoloading**.

---

# 12. `composer.json`

A Laravel project's `composer.json` contains configuration similar to:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
}
```

The important part is:

```text
App\ → app/
```

This tells Composer:

> Classes beginning with `App\` can be found inside the `app/` directory according to PSR-4 rules.

---

# 13. PSR-4

PSR-4 defines a standard way of mapping namespaces to filesystem paths.

For example:

```text
Namespace:
App\Services\PaymentService

↓
Filesystem:

app/
└── Services/
    └── PaymentService.php
```

Another:

```text
App\Models\User

↓

app/Models/User.php
```

Another:

```text
App\Http\Controllers\OrderController

↓

app/Http/Controllers/OrderController.php
```

This consistency is extremely useful.

---

# 14. PSR-4 Mental Model

If Composer has:

```text
App\ → app/
```

then:

```text
App\Services\OrderService
```

maps to:

```text
app/Services/OrderService.php
```

Break it down:

```text
App\                → app/
Services\            → Services/
OrderService         → OrderService.php
```

So:

```text
App\Services\OrderService
           ↓
app/Services/OrderService.php
```

---

# 15. What Is `vendor/autoload.php`?

Composer generates:

```text
vendor/autoload.php
```

This file initializes Composer's autoloader.

In a plain PHP project, you might write:

```php
require __DIR__ . '/vendor/autoload.php';
```

Then Composer can automatically load your configured classes and installed packages.

Laravel's application bootstrapping handles this for you.

---

# 16. Without Autoloading

Imagine:

```text
project/
├── index.php
├── User.php
├── Order.php
└── PaymentService.php
```

You might have to write:

```php
require_once 'User.php';
require_once 'Order.php';
require_once 'PaymentService.php';
```

Then another file might need:

```php
require_once 'User.php';
require_once 'Order.php';
```

As the project grows, this becomes messy.

---

# 17. With Autoloading

With Composer:

```php
$user = new User();
```

If the class isn't loaded yet, Composer's autoloader can locate the corresponding file and load it.

Conceptually:

```text
new App\Models\User()
          │
          ▼
Composer Autoloader
          │
          ▼
app/Models/User.php
          │
          ▼
User class loaded
```

You don't manually `require` every class.

---

# 18. How Composer Knows

Suppose:

```json
"App\\": "app/"
```

And PHP asks for:

```text
App\Models\User
```

Composer essentially maps:

```text
App\
```

to:

```text
app/
```

Then:

```text
Models\User
```

becomes:

```text
Models/User.php
```

Result:

```text
app/Models/User.php
```

That's the core idea of PSR-4 autoloading.

---

# 19. Running Composer Autoload Generation

If you add or change autoload configuration, run:

```bash
composer dump-autoload
```

Composer regenerates its autoload information.

In many normal Laravel workflows, when you're simply creating classes under the existing `App\` → `app/` PSR-4 mapping, you generally don't need to run this manually.

But if you change `composer.json` autoload configuration, you should regenerate it.

---

# 20. `composer install` vs `composer dump-autoload`

Important distinction.

### `composer install`

Installs the project's dependencies based on `composer.lock`.

Typically used when setting up a project.

### `composer update`

Updates dependencies according to the version constraints and modifies the lock file.

Be careful with this in production.

### `composer dump-autoload`

Regenerates Composer's autoload files.

It doesn't mean:

> "Install all packages again."

---

# 21. Laravel's `app/` Directory

You'll commonly see:

```text
app/
├── Console/
├── Exceptions/
├── Http/
├── Models/
├── Providers/
└── Services/
```

These classes typically use:

```text
App\
```

as their root namespace.

For example:

```php
namespace App\Models;
```

maps under the default Laravel PSR-4 configuration to:

```text
app/Models/
```

---

# 22. Why This Matters for Laravel

When you write:

```php
use App\Services\OrderService;
```

you don't need:

```php
require_once 'app/Services/OrderService.php';
```

Laravel + Composer's autoloading infrastructure handles class loading.

That's why you can focus on:

```php
$orderService->create();
```

instead of manually managing PHP files.

---

# 23. Namespaces Are Also Important for Organization

Imagine a large application:

```text
App\
├── Models\
├── Services\
├── Repositories\
├── Contracts\
├── Jobs\
├── Events\
├── Listeners\
├── Notifications\
└── Http\
```

Namespaces communicate where a class belongs conceptually.

For example:

```php
namespace App\Services;
```

immediately tells another developer:

> This class belongs to the application's service layer.

---

# 24. Common Mistake: Namespace Doesn't Match Class Location

Suppose the file is:

```text
app/Services/OrderService.php
```

but you write:

```php
namespace App\Controllers;
```

That is inconsistent with the expected PSR-4 mapping.

If the class is:

```php
namespace App\Services;

class OrderService
{
}
```

then the expected path under the default mapping is:

```text
app/Services/OrderService.php
```

Keep namespace and filesystem structure consistent.

---

# 25. Common Mistake: Forgetting `use`

Suppose your file contains:

```php
namespace App\Http\Controllers;
```

and you want:

```text
App\Services\OrderService
```

Writing:

```php
$orderService = new OrderService();
```

without importing it can cause PHP to look for:

```text
App\Http\Controllers\OrderService
```

instead.

So write:

```php
use App\Services\OrderService;
```

Then:

```php
$orderService = new OrderService();
```

---

# 26. Common Mistake: Confusing Namespace With Import

This:

```php
namespace App\Services;
```

means:

> This class belongs to the `App\Services` namespace.

This:

```php
use App\Models\User;
```

means:

> Import/alias that class so I can refer to it as `User` in this file.

They have different purposes.

---

# 27. Another Important Point: `use` Does Not Load the File

This is subtle but important.

When you write:

```php
use App\Models\User;
```

`use` itself doesn't mean:

> Load `User.php` immediately.

It establishes a name alias in the current file.

Composer's autoloader is what can locate/load the class when PHP actually needs it.

Think:

```text
use
 ↓
Name resolution

Composer autoloader
 ↓
Class file loading
```

---

# 28. PHP Example From Scratch

Project:

```text
my-project/
├── composer.json
├── index.php
└── src/
    └── User.php
```

`composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

`User.php`:

```php
<?php

declare(strict_types=1);

namespace App;

final class User
{
    public function __construct(
        public string $name,
    ) {
    }
}
```

Run:

```bash
composer dump-autoload
```

Then `index.php`:

```php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\User;

$user = new User('John');

echo $user->name;
```

Output:

```text
John
```

No:

```php
require 'src/User.php';
```

That's Composer autoloading.

---

# 29. Laravel Version

Laravel already comes with Composer configuration and autoloading.

So you normally create:

```text
app/Services/OrderService.php
```

with:

```php
<?php

declare(strict_types=1);

namespace App\Services;

final class OrderService
{
}
```

Then elsewhere:

```php
use App\Services\OrderService;
```

and:

```php
$orderService = new OrderService();
```

That's it.

---

# 30. Performance

Autoloading is generally efficient and is vastly more maintainable than manually requiring every class.

For production deployments, Composer can optimize the autoloader:

```bash
composer install --no-dev --optimize-autoloader
```

You may also encounter:

```bash
composer dump-autoload -o
```

The `-o` option optimizes the autoloader.

We'll discuss deployment optimization later.

---

# 🔐 Security

Composer dependencies are part of your application's supply chain.

Best practices:

* Commit `composer.lock` for applications.
* Keep dependencies updated.
* Avoid installing unnecessary packages.
* Review packages before trusting them.
* Don't blindly run arbitrary Composer scripts from unknown packages.
* Use production-oriented installation options during deployment.

Autoloading itself isn't a security mechanism.

---

# 🧠 Interview Questions

Try answering these:

1. What is a namespace?
2. Why do we need namespaces?
3. What does `use` do?
4. What's the difference between `namespace` and `use`?
5. What is Composer?
6. What is autoloading?
7. What is PSR-4?
8. What does `App\\` → `app/` mean?
9. What is `vendor/autoload.php`?
10. What does `composer dump-autoload` do?
11. What's the difference between `composer install` and `composer update`?
12. Why don't we manually `require` every Laravel class?
13. What happens if a namespace doesn't match the PSR-4 directory structure?
14. Does `use App\Models\User;` itself load `User.php`?

---

# 🎯 Practice Exercise

Create this project structure:

```text
php-oop/
├── composer.json
├── index.php
└── src/
    ├── Models/
    │   └── Product.php
    └── Services/
        └── ProductService.php
```

Configure:

```text
App\ → src/
```

Create:

```php
App\Models\Product
```

with:

```text
name
price
```

Then create:

```php
App\Services\ProductService
```

that accepts a `Product` object.

Finally, in `index.php`:

```php
use App\Models\Product;
use App\Services\ProductService;
```

Create the objects and make the service display the product.

---

# 🔥 Challenge

Build:

```text
src/
├── Models/
│   ├── Product.php
│   └── User.php
│
├── Contracts/
│   └── PaymentGateway.php
│
├── Payments/
│   └── StripePaymentGateway.php
│
└── Services/
    └── CheckoutService.php
```

Use proper namespaces:

```text
App\Models
App\Contracts
App\Payments
App\Services
```

Then build this dependency chain:

```text
CheckoutService
       ↓
PaymentGateway
       ↑
StripePaymentGateway
```

Your `index.php` should only need the appropriate `use` statements and object creation.

This challenge combines the concepts we've learned:

```text
Classes
   ↓
Interfaces
   ↓
Dependency Injection
   ↓
Namespaces
   ↓
Composer Autoloading
```

---

# 📌 What You Should Remember

The four most important things from today's lesson:

```text
1. namespace
   ↓
   Organizes a class's name

2. use
   ↓
   Lets you reference another class conveniently

3. PSR-4
   ↓
   Defines namespace → file path mapping

4. Composer autoloading
   ↓
   Automatically loads classes when needed
```

The complete Laravel picture:

```text
app/Services/OrderService.php
          │
          ▼
namespace App\Services;
          │
          ▼
Composer PSR-4
          │
          ▼
App\ → app/
          │
          ▼
use App\Services\OrderService;
          │
          ▼
OrderService
```

## 🚀 Next Lesson: Exceptions & Error Handling

We'll learn:

```text
try
catch
finally
throw
Exception
custom exceptions
exception hierarchy
```

Then we'll apply them to **real Laravel service/business logic**, including how to avoid hiding errors and how Laravel turns exceptions into HTTP responses.
