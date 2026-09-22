# Lesson 30 — Laravel Blade & Views 🎨

You now understand the backend flow:

```text
Request
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
Database
```

But a web application also needs to **display HTML to the user**.

That's where **Blade** comes in.

---

# 1. What Is Blade?

Blade is Laravel's **server-side templating engine**.

It lets you write HTML while using PHP/Laravel features inside the HTML.

For example:

```blade
<h1>{{ $todo->title }}</h1>
```

Laravel processes this and produces normal HTML:

```html
<h1>Learn Laravel</h1>
```

Think:

```text
Controller
    ↓
Data
    ↓
Blade
    ↓
HTML
    ↓
Browser
```

---

# 2. Real-World Analogy

Imagine a restaurant menu.

The menu has a fixed structure:

```text
+-----------------------+
|       Restaurant      |
|-----------------------|
| Burger       $10      |
| Pizza        $12      |
| Coffee        $4      |
+-----------------------+
```

But the actual products change.

Blade works similarly.

The **HTML structure** is your template:

```blade
<h1>Products</h1>

@foreach ($products as $product)
    <p>{{ $product->name }}</p>
@endforeach
```

The data changes dynamically.

```text
Template
   +
Data
   ↓
Rendered HTML
```

---

# 3. Where Blade Files Live

Blade templates normally live inside:

```text
resources/views/
```

For example:

```text
resources/
└── views/
    ├── welcome.blade.php
    ├── todos/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   └── create.blade.php
    └── layouts/
        └── app.blade.php
```

The `.blade.php` extension tells Laravel:

> This is a Blade template.

---

# 4. Returning a View

Controller:

```php
public function index(): View
{
    return view('todos.index');
}
```

Laravel looks for:

```text
resources/views/todos/index.blade.php
```

So:

```php
view('todos.index')
```

maps to:

```text
todos/index.blade.php
```

The dot notation represents directories.

---

# 5. Passing Data to Blade

Controller:

```php
public function show(Todo $todo): View
{
    return view('todos.show', [
        'todo' => $todo,
    ]);
}
```

Blade:

```blade
<h1>{{ $todo->title }}</h1>
```

Flow:

```text
Controller
    ↓
Todo object
    ↓
View
    ↓
Blade
    ↓
HTML
```

---

# 6. Alternative Data Syntax

You can also write:

```php
return view('todos.show')
    ->with('todo', $todo);
```

But for multiple values:

```php
return view('todos.index', [
    'todos' => $todos,
    'user' => $user,
]);
```

is generally clearer.

---

# 7. Blade Echo Syntax

The most common Blade syntax is:

```blade
{{ $name }}
```

Example:

```blade
<h1>{{ $todo->title }}</h1>
```

Blade automatically escapes normal `{{ }}` output.

This is extremely important for security.

---

# 8. Why Escaping Matters 🔐

Suppose a malicious user enters:

```text
<script>alert('Hacked')</script>
```

If you output user-controlled data unsafely, the browser might execute it.

Blade's normal syntax:

```blade
{{ $title }}
```

escapes HTML.

So malicious content is treated as text rather than executable HTML.

Conceptually:

```text
User input
    ↓
{{ $value }}
    ↓
HTML escaping
    ↓
Safe HTML output
```

This helps protect against **XSS — Cross-Site Scripting**.

---

# 9. Raw HTML Output

Blade also supports:

```blade
{!! $html !!}
```

This means:

> Don't escape the HTML.

Example:

```blade
{!! $html !!}
```

### ⚠️ Dangerous

Never do this with untrusted user input.

Bad:

```blade
{!! $user->bio !!}
```

if users can enter arbitrary HTML.

Prefer:

```blade
{{ $user->bio }}
```

unless you intentionally sanitize/allow HTML.

---

# 10. Blade Conditions

Instead of PHP:

```php
<?php if ($todo->completed): ?>
    ...
<?php endif; ?>
```

Blade provides:

```blade
@if ($todo->completed)
    <span>Completed</span>
@else
    <span>Pending</span>
@endif
```

Much cleaner for templates.

---

# 11. `@elseif`

```blade
@if ($todo->priority === 1)
    <span>Low</span>
@elseif ($todo->priority === 2)
    <span>Medium</span>
@else
    <span>High</span>
@endif
```

---

# 12. `@unless`

You can write:

```blade
@unless ($todo->completed)
    <span>Not completed</span>
@endunless
```

Meaning:

```text
if NOT completed
```

---

# 13. `@isset`

```blade
@isset($todo)
    <h1>{{ $todo->title }}</h1>
@endisset
```

Useful when a variable may not exist.

---

# 14. Loops

### `@foreach`

```blade
@foreach ($todos as $todo)
    <h2>{{ $todo->title }}</h2>
@endforeach
```

This is the most common loop in Blade.

---

# 15. `@forelse`

One particularly useful directive:

```blade
@forelse ($todos as $todo)
    <h2>{{ $todo->title }}</h2>
@empty
    <p>No todos found.</p>
@endforelse
```

This is excellent for lists.

Instead of:

```blade
@if ($todos->isEmpty())
    <p>No todos found.</p>
@else
    @foreach ($todos as $todo)
        ...
    @endforeach
@endif
```

you can use:

```blade
@forelse
```

---

# 16. `@for`

```blade
@for ($i = 1; $i <= 5; $i++)
    <p>{{ $i }}</p>
@endfor
```

Use it when you genuinely need a counter.

For database records, prefer:

```blade
@foreach
```

---

# 17. `@foreach` With Keys

```blade
@foreach ($todos as $todo)
    <div>
        {{ $todo->id }} - {{ $todo->title }}
    </div>
@endforeach
```

If you need the key:

```blade
@foreach ($todos as $key => $todo)
    <p>{{ $key }}: {{ $todo->title }}</p>
@endforeach
```

---

# 18. Loop Variable `$loop`

Blade provides a special `$loop` variable.

Example:

```blade
@foreach ($todos as $todo)
    <p>
        {{ $loop->iteration }}.
        {{ $todo->title }}
    </p>
@endforeach
```

You can use properties such as:

```text
$loop->index
$loop->iteration
$loop->remaining
$loop->count
$loop->first
$loop->last
```

Example:

```blade
@foreach ($todos as $todo)
    @if ($loop->first)
        <strong>First Todo</strong>
    @endif

    <p>{{ $todo->title }}</p>
@endforeach
```

---

# 19. Blade Layouts ⭐⭐⭐

Imagine every page needs:

```text
HTML
 ├── head
 ├── navigation
 ├── content
 └── footer
```

You don't want to duplicate that in every file.

Create:

```text
resources/views/layouts/app.blade.php
```

Example:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        @yield('title')
    </title>
</head>

<body>

    <nav>
        <a href="{{ route('todos.index') }}">
            Todos
        </a>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
```

---

# 20. Extending a Layout

Now:

```text
resources/views/todos/index.blade.php
```

```blade
@extends('layouts.app')

@section('title', 'Todos')

@section('content')

    <h1>Todo List</h1>

@endsection
```

The final structure becomes:

```text
layouts/app.blade.php
        +
todos/index.blade.php
        ↓
Complete HTML
```

---

# 21. Why Layouts Matter

Without layouts:

```text
index.blade.php
   → HTML head
   → navbar
   → footer

show.blade.php
   → HTML head
   → navbar
   → footer

create.blade.php
   → HTML head
   → navbar
   → footer
```

Duplication.

With layout:

```text
             app.blade.php
            /      |      \
           /       |       \
       index      show     create
```

One place controls shared structure.

---

# 22. Blade Components ⭐⭐⭐

Modern Laravel applications also make heavy use of Blade components.

For example:

```blade
<x-button>
    Save Todo
</x-button>
```

You can create a component:

```bash
php artisan make:component Button
```

This gives you a reusable component structure.

Conceptually:

```text
resources/views/components/
    button.blade.php
```

Then:

```blade
<x-button>
    Save Todo
</x-button>
```

---

# 23. Why Components?

Imagine you have 50 buttons.

Without components:

```html
<button class="...">Save</button>
<button class="...">Delete</button>
<button class="...">Edit</button>
...
```

With components:

```blade
<x-button>
    Save
</x-button>
```

You can centralize:

- styling
- attributes
- accessibility
- structure

---

# 24. Component Props

A component can receive data.

Usage:

```blade
<x-alert type="success">
    Todo created successfully.
</x-alert>
```

The component can use:

```php
$type
```

and:

```blade
{{ $slot }}
```

for the content.

---

# 25. Slots

Consider:

```blade
<x-card>
    <h2>Todo</h2>

    <p>Learn Laravel</p>
</x-card>
```

Inside the component:

```blade
<div class="card">
    {{ $slot }}
</div>
```

The content between:

```blade
<x-card>
```

and:

```blade
</x-card>
```

becomes:

```text
$slot
```

This makes components reusable.

---

# 26. Named Slots

You can have multiple sections.

Example:

```blade
<x-card>
    <x-slot:title>
        Todo
    </x-slot:title>

    <p>Learn Laravel</p>
</x-card>
```

Component:

```blade
<div>
    <h2>{{ $title }}</h2>

    {{ $slot }}
</div>
```

Now the component has:

```text
title
content
```

---

# 27. Forms

Suppose we want to create a Todo.

```blade
<form
    method="POST"
    action="{{ route('todos.store') }}"
>
    @csrf

    <input
        type="text"
        name="title"
    >

    <button type="submit">
        Create Todo
    </button>
</form>
```

Notice:

```blade
@csrf
```

This is extremely important.

---

# 28. What Is CSRF?

CSRF means:

> Cross-Site Request Forgery.

Imagine you're logged into:

```text
example.com
```

and a malicious website tries to trick your browser into submitting:

```text
POST /todos
```

as you.

Laravel uses CSRF tokens to help ensure the request came from your application.

```blade
@csrf
```

generates the hidden token.

Conceptually:

```text
Form
 ↓
CSRF token
 ↓
POST
 ↓
Laravel verifies token
 ↓
Valid request
```

---

# 29. Why `@csrf` Matters

For POST forms:

```blade
<form method="POST">
    @csrf
```

For PUT/PATCH/DELETE forms, browsers traditionally don't submit those methods directly through HTML forms.

Laravel provides:

```blade
@method('PUT')
```

Example:

```blade
<form
    method="POST"
    action="{{ route('todos.update', $todo) }}"
>
    @csrf
    @method('PUT')

    <input
        type="text"
        name="title"
        value="{{ $todo->title }}"
    >

    <button type="submit">
        Update
    </button>
</form>
```

Laravel interprets this as a PUT request.

---

# 30. Displaying Validation Errors

Suppose:

```php
'title' => [
    'required',
    'max:255',
]
```

fails.

Blade can display errors:

```blade
@error('title')
    <p>{{ $message }}</p>
@enderror
```

This is much better than manually checking validation state.

---

# 31. Preserve Old Input

Suppose the user submits:

```text
title = ""
```

and validation fails.

You don't want them to retype everything.

Use:

```blade
<input
    type="text"
    name="title"
    value="{{ old('title') }}"
>
```

You can provide a default:

```blade
value="{{ old('title', $todo->title) }}"
```

Meaning:

```text
old input exists?
   ↓
use it
otherwise
   ↓
use $todo->title
```

---

# 32. Complete Todo Form

A practical example:

```blade
<form
    method="POST"
    action="{{ route('todos.store') }}"
>
    @csrf

    <div>
        <label for="title">
            Title
        </label>

        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title') }}"
        >

        @error('title')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">
        Create Todo
    </button>
</form>
```

This is a realistic Blade form.

---

# 33. Blade and XSS

Let's say:

```php
$title = '<script>alert("XSS")</script>';
```

This:

```blade
{{ $title }}
```

escapes the content.

But:

```blade
{!! $title !!}
```

renders raw HTML.

Therefore:

### Default

```blade
{{ $value }}
```

### Raw HTML

```blade
{!! $value !!}
```

Only use raw HTML when you have a trusted/sanitized source.

---

# 34. Blade Directives

Laravel provides many directives.

Examples:

```blade
@if
@else
@elseif
@endif

@foreach
@forelse
@endforeach
@endforelse

@auth
@endauth

@guest
@endguest

@csrf
@method

@error
@enderror

@include
@extends
@section
@yield
```

Don't memorize everything.

Learn the patterns and use the documentation when necessary.

---

# 35. `@auth` and `@guest`

Show something only to authenticated users:

```blade
@auth
    <a href="{{ route('todos.index') }}">
        My Todos
    </a>
@endauth
```

For guests:

```blade
@guest
    <a href="{{ route('login') }}">
        Login
    </a>
@endguest
```

This controls **presentation**.

It is NOT a replacement for backend authorization.

---

# 36. Important Security Rule

This:

```blade
@auth
    <button>Delete</button>
@endauth
```

doesn't mean the user is authorized to delete the resource.

A malicious user can still call your endpoint directly.

Therefore:

```text
Blade
   ↓
UI visibility

Policy / Authorization
   ↓
Actual security
```

Never rely on hiding a button as your authorization system.

---

# 37. `@can`

Blade can integrate with Laravel authorization:

```blade
@can('update', $todo)
    <a href="{{ route('todos.edit', $todo) }}">
        Edit
    </a>
@endcan
```

This means:

> Show the button if the current user is authorized.

Again, the backend authorization still matters.

---

# 38. Blade Includes

You can split reusable template sections.

For example:

```text
resources/views/todos/
├── index.blade.php
├── show.blade.php
└── _form.blade.php
```

Then:

```blade
@include('todos._form')
```

This can be useful for simple reusable fragments.

For more reusable UI with behavior/props, Blade components are often a better fit.

---

# 39. View Data Flow

Let's look at the full flow.

Controller:

```php
public function index(): View
{
    $todos = Todo::query()
        ->latest()
        ->get();

    return view('todos.index', [
        'todos' => $todos,
    ]);
}
```

Blade:

```blade
@forelse ($todos as $todo)

    <h2>{{ $todo->title }}</h2>

@empty

    <p>No todos found.</p>

@endforelse
```

Flow:

```text
Database
   ↓
Eloquent
   ↓
Controller
   ↓
View data
   ↓
Blade
   ↓
HTML
   ↓
Browser
```

---

# 40. Don't Put Database Queries in Blade ❌

Bad:

```blade
@foreach (Todo::all() as $todo)
    {{ $todo->title }}
@endforeach
```

Why is this bad?

The view is now responsible for data access.

Instead:

```text
Controller / Service
       ↓
     Query
       ↓
      View
```

Blade should focus on presentation.

---

# 41. N+1 Problem in Views ⚠️

This is a very important performance issue.

Suppose:

```blade
@foreach ($todos as $todo)
    {{ $todo->user->name }}
@endforeach
```

If `user` wasn't eager loaded, this could trigger:

```text
1 query → todos

+
N queries → users
```

For 100 Todos:

```text
1 + 100
=
101 queries
```

Instead:

```php
$todos = Todo::query()
    ->with('user')
    ->get();
```

Now the data is loaded efficiently.

This is why performance isn't only a database-layer concern—the view can expose N+1 problems.

---

# 42. Blade vs API

Blade is generally used when Laravel renders HTML:

```text
Browser
   ↓
Laravel
   ↓
Blade
   ↓
HTML
```

For an API:

```text
Mobile App
   ↓
Laravel API
   ↓
JSON
```

You generally don't use Blade for API responses.

Later we'll study:

```text
API Resource
```

for transforming Eloquent models into clean JSON.

---

# 43. Raw PHP vs Blade

### Raw PHP

```php
<?php if ($todo->completed): ?>

    <span>Completed</span>

<?php else: ?>

    <span>Pending</span>

<?php endif; ?>
```

Blade:

```blade
@if ($todo->completed)
    <span>Completed</span>
@else
    <span>Pending</span>
@endif
```

Blade is essentially a cleaner templating syntax built around PHP.

It doesn't replace PHP itself.

---

# 44. Behind the Scenes

When you write:

```blade
<h1>{{ $todo->title }}</h1>
```

Laravel doesn't send the Blade syntax directly to the browser.

Conceptually:

```text
Blade template
      ↓
Blade compiler
      ↓
Compiled PHP
      ↓
PHP execution
      ↓
HTML
      ↓
Browser
```

So the browser receives:

```html
<h1>Learn Laravel</h1>
```

not:

```blade
<h1>{{ $todo->title }}</h1>
```

---

# 45. Blade Compilation and Performance

Laravel compiles Blade templates into PHP.

Compiled views are cached, so Laravel doesn't need to reinterpret the Blade template from scratch on every request in the same way.

In production, keeping views simple and avoiding expensive operations inside them is still important.

The biggest performance concern is usually not Blade syntax itself.

It's things like:

```text
N+1 queries
Large datasets
Expensive calculations
Huge HTML responses
```

---

# 46. A Professional Todo Page

Controller:

```php
public function index(): View
{
    $todos = $this->todoService->listForUser(
        auth()->user()
    );

    return view('todos.index', [
        'todos' => $todos,
    ]);
}
```

Blade:

```blade
@extends('layouts.app')

@section('title', 'My Todos')

@section('content')

    <h1>My Todos</h1>

    <a href="{{ route('todos.create') }}">
        Create Todo
    </a>

    @forelse ($todos as $todo)

        <article>
            <h2>
                {{ $todo->title }}
            </h2>

            @if ($todo->completed)
                <span>Completed</span>
            @else
                <span>Pending</span>
            @endif

            @can('update', $todo)
                <a href="{{ route('todos.edit', $todo) }}">
                    Edit
                </a>
            @endcan
        </article>

    @empty

        <p>No todos found.</p>

    @endforelse

@endsection
```

Notice the separation:

```text
Controller
→ obtains data

Service
→ business logic

Blade
→ presentation
```

---

# 47. Common Mistakes ❌

### 1. Raw HTML for user input

Bad:

```blade
{!! $user->name !!}
```

Use:

```blade
{{ $user->name }}
```

unless raw HTML is intentionally trusted/sanitized.

---

### 2. Database queries in views

Bad:

```blade
{{ Todo::find(10)->title }}
```

Move database access out of the view.

---

### 3. Complex business logic in Blade

Bad:

```blade
@php
    // 50 lines of business logic
@endphp
```

If your Blade file starts looking like a service class, architecture has gone wrong.

---

### 4. Forgetting CSRF

Bad:

```blade
<form method="POST">
```

Better:

```blade
<form method="POST">
    @csrf
```

---

### 5. Trusting UI authorization

Hiding:

```blade
<button>Delete</button>
```

doesn't secure the endpoint.

Always enforce authorization server-side.

---

### 6. N+1 queries

Bad:

```blade
@foreach ($todos as $todo)
    {{ $todo->user->name }}
@endforeach
```

without proper eager loading.

---

# 48. Security Checklist 🔐

For Blade applications:

```text
✓ Use {{ }} for normal output
✓ Use {!! !!} only for trusted/sanitized HTML
✓ Use @csrf for state-changing forms
✓ Validate all input
✓ Authorize actions server-side
✓ Don't trust hidden form fields
✓ Don't put secrets in views
✓ Avoid database queries in views
✓ Escape user-generated content
```

---

# 49. Interview Questions 🎯

### Q1. What is Blade?

Laravel's server-side templating engine.

### Q2. Where are Blade files stored?

Usually:

```text
resources/views/
```

### Q3. Difference between `{{ }}` and `{!! !!}`?

```text
{{ }}
→ escaped output

{!! !!}
→ raw/unescaped output
```

### Q4. Why is `{{ }}` safer?

It escapes HTML output, helping prevent XSS when displaying untrusted content.

### Q5. What does `@csrf` do?

Adds a CSRF token to a form so Laravel can validate the request's authenticity.

### Q6. What is `@extends`?

It allows a Blade view to inherit a layout.

### Q7. What is `@yield`?

It defines a section in a layout where child-view content can be inserted.

### Q8. What is `@section`?

It supplies content for a layout section.

### Q9. What is a Blade component?

A reusable UI/template component that can receive data and slots.

### Q10. Should database queries be performed in Blade?

Generally no. Keep data access outside the presentation layer.

---

# 50. Small Exercise 📝

Create:

```text
resources/views/layouts/app.blade.php
```

with:

```text
HTML
Navigation
@yield('title')
@yield('content')
```

Then create:

```text
resources/views/todos/index.blade.php
```

Requirements:

1. Extend the layout.
2. Show `"My Todos"`.
3. Loop through `$todos`.
4. Display title.
5. Show `"Completed"` or `"Pending"`.
6. Show `"No todos found"` if the collection is empty.
7. Add a link using a named route.

---

# 51. Challenge 🚀

Build a complete Todo creation page.

### Controller

```text
GET /todos/create
```

returns:

```text
todos.create
```

### Blade

Create:

```text
resources/views/todos/create.blade.php
```

Requirements:

```text
Title input
Description textarea
Priority select
Submit button
CSRF protection
Validation error messages
Old input preservation
```

Example structure:

```blade
<form method="POST" action="{{ route('todos.store') }}">
    @csrf

    <!-- title -->

    <!-- description -->

    <!-- priority -->

    <button type="submit">
        Create Todo
    </button>
</form>
```

Your goal is to make the complete flow:

```text
GET /todos/create
       ↓
Route
       ↓
Middleware
       ↓
TodoController@create
       ↓
Blade
       ↓
HTML Form

          ↓ Submit

POST /todos
       ↓
auth
       ↓
StoreTodoRequest
       ↓
Validation
       ↓
TodoController@store
       ↓
DTO
       ↓
TodoService
       ↓
Database
       ↓
Redirect
```

This is now a **real Laravel application flow**, not just isolated syntax.

---

# 52. Summary

You've now learned the Laravel presentation layer:

```text
Controller
    ↓
View Data
    ↓
Blade
    ↓
HTML
    ↓
Browser
```

The important concepts are:

- Blade templates
- `view()`
- passing data to views
- `{{ }}`
- `{!! !!}`
- `@if`
- `@foreach`
- `@forelse`
- `$loop`
- layouts
- `@extends`
- `@section`
- `@yield`
- Blade components
- slots
- forms
- `@csrf`
- `@method`
- validation errors
- `old()`
- `@auth`
- `@guest`
- `@can`
- XSS protection
- CSRF protection
- N+1 queries
- Blade performance
- Blade vs API responses

### The professional mental model

> **Blade is for presentation, not business logic.**

Keep this separation:

```text
Blade
  ↓
"How should this data be displayed?"

Service
  ↓
"What should the application do?"

Repository/Eloquent
  ↓
"How do we retrieve/store the data?"
```

That separation becomes extremely important as your Laravel applications grow.

---

## Next Lesson → Lesson 31: Laravel Service Container & Service Providers ⚙️

We'll go much deeper into the mechanism that powers Laravel's dependency injection:

```text
Controller
     ↓
Service Container
     ↓
Dependency Resolution
     ↓
Bindings
     ↓
Interfaces
     ↓
Implementations
```

We'll cover `bind()`, `singleton()`, `scoped()`, contextual bindings, automatic resolution, service providers, `register()` vs `boot()`, deferred services, and a real Todo application example.
