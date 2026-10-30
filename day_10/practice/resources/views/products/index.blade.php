<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius);
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.15s ease;
            display: inline-block;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .search-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        input[type="search"] {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.15s ease;
            background-color: #fff;
        }

        input[type="search"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 0.5rem;
        }

        ol {
            padding-left: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        li {
            font-size: 0.9375rem;
            color: var(--text-main);
        }

        li a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        li a:hover {
            text-decoration: underline;
        }

        .empty-state {
            list-style: none;
            color: var(--text-muted);
            text-align: center;
            padding: 1rem;
            margin-left: -1.25rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Products</h1>
            <a href="/products/new" class="btn">Add Product</a>
        </header>

        <!-- Search Section -->
        <section class="card">
            <form action="{{ url('/products') }}" method="GET" class="search-form">
                <div class="form-group">
                    <label for="search">Write Your Query</label>
                    <input value="{{ $search ?? '' }}" type="search" name="search" id="search" placeholder="Search products...">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">Search</button>
                    <button type="button" class="btn btn-secondary" onclick="handleReset()">Reset</button>
                </div>
            </form>
        </section>

        <!-- Product List Section -->
        <section class="card">
            <ol>
                @forelse ($products as $product)
                    <li>
                        <a href="/products/{{ $product['slug'] }}">{{ $product['name'] }}</a> 
                        - <strong>${{$product['price']}}</strong>
                    </li>
                @empty
                    <li class="empty-state">No products found.</li>
                @endforelse
            </ol>
        </section>
    </div>

    <script>
        function handleReset(){
            window.location.href = "{{ url('/products') }}";
        }
    </script>
</body>
</html>