<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
</head>
<body>
    <h1>Products</h1>

    <form action="" style="max-width: 300px;">
        <div style="display: grid; gap: 10px;">
            <label for="search">Write Your Query</label>
            <input type="search" name="search" id="search">
        </div>
    </form>

    <ol>
        @forelse ($products as $slug => $product)
            <li style="list-style: decimal">
                <a href="/products/{{ $slug }}">{{ $product['name'] }}</a>
                - ${{ $product['price'] }}
            </li>
        @empty
            <li>No products found.</li>
        @endforelse
    </ul>

</body>
</html>