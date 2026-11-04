<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Product - {{ $product->name }}</title>
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
            max-width: 500px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-main);
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
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

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            background-color: #fff;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button[type="submit"] {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.15s ease;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: var(--primary-hover);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: var(--primary);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/products/{{ $product->slug }}" class="back-link">&larr; Back to Product</a>
        <div class="card">
            <h4>Edit Product</h4>
            
            <form id="updateProductForm" action="{{ url('/products/update' . $product->id) }}" method="POST" class="form-grid">
                @csrf
                @method('PUT')

                <input name='slug' id="slug" style="display: none" value="{{old('slug',$product->slug)}}" >
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required /> 
                </div>

                <div class="form-group">
                    <label for="cat_id">Category / Code</label>
                    <select name="cat_id" id="cat_id" required>
                        <option value="">Select a category</option>
                        @foreach($codes as $code)
                            <option value="{{ $code->id }}" {{ (old('cat_id', $product->cat_id) == $code->id) ? 'selected' : '' }}>
                                {{ $code->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}" required> 
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <button type="submit">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('updateProductForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch(this.action, {
                method: 'POST', // Using POST with _method=PUT hidden field
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                alert('Product updated successfully!');
                window.location.href = "{{ url('/products') }}";
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong!');
            });
        });
    </script>
</body>
</html>