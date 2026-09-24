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
        <a href="{{ route('todos.store') }}">
            Add Todo
        </a>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
