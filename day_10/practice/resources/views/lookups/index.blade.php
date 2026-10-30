<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lookups</title>
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
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--text-main);
        }

        .form-grid {
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

        input[type="text"], select {
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

        input[type="text"]:focus, select:focus {
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
            align-self: flex-start;
        }

        button[type="submit"]:hover {
            background-color: var(--primary-hover);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        th {
            background-color: #f1f5f9;
            color: var(--text-muted);
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty-cell {
            text-align: center;
            color: var(--text-muted);
            padding: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Lookup Form Section -->
        <section class="card">
            <h4>Lookup Form</h4>
            <form id="handleForm" action="{{url('/lookups')}}" method="POST" class="form-grid">
                @csrf
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" placeholder="Enter name" required /> 
                </div>

                <div class="form-group">
                    <label for="ftype">Type</label>
                    <select name="type" id="ftype">
                        <option value="product">Product</option>    
                        <option value="others">Others</option>    
                    </select> 
                </div>

                <div class="form-group">
                    <label for="desc">Description</label>
                    <input type="text" name="description" id="desc" placeholder="Enter description" /> 
                </div>

                <div>
                    <button type="submit">Submit</button>
                </div>
            </form>
        </section>

        <!-- Lookup List Section -->
        <section class="card">
            <h4>Lookup List</h4>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lookups as $lookup)
                    <tr>
                        <td>{{ $lookup->name }}</td>
                        <td>{{ $lookup->type }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="empty-cell">No lookups found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>

    <script>
        document.getElementById('handleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                alert('Code added successfully');
                this.reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong!');
            });
        });
    </script>
</body>
</html>