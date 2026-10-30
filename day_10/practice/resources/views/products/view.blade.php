<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $product['name'] ?? $product->name }}</title>
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
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .back-link {
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--primary);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.25rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .meta-info {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .price-badge {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary);
            background-color: #eff6ff;
            padding: 0.5rem 0.875rem;
            border-radius: var(--radius);
            white-space: nowrap;
        }

        .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .description {
            font-size: 0.9375rem;
            color: var(--text-main);
            line-height: 1.6;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <a href="/products" class="back-link">&larr; Back to products</a>
        
        <div class="card">
            <div class="product-header">
                <div>
                    <h1>{{ $product['name'] ?? $product->name }}</h1>
                    <span class="meta-info">Slug: <code>{{ $slug }}</code></span>
                </div>
                <div class="price-badge">
                    ${{ number_format($product['price'] ?? $product->price, 2) }}
                </div>
            </div>

            <div>
                <h3 class="section-title">Description</h3>
                <p class="description">{{ $product['description'] ?? $product->description ?: 'No description provided.' }}</p>
            </div>
        </div>
    </div>

</body>
</html>