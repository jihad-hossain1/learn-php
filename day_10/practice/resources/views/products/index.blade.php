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

    <form 
    {{-- onsubmit="handleSubmit(event)" --}}
    action="{{url('/products')}}" 
    method="GET"
    style="max-width: 300px;"
    >
        <div style="display: grid; gap: 10px;">
            <label for="search">Write Your Query</label>
            <input value="{{$search}}" type="search" name="search" id="search">
        </div>
         <button type="submit">Search</button>
         <button type="reset" onclick="handleReset()">Reset</button>
    </form>

    <ol>
        @forelse ($products as $product)
            <li style="list-style: decimal">
                <a href="/products/{{ $product['slug'] }}">{{ $product['name'] }}</a>
                - ${{ $product['price'] }}
            </li>
        @empty
            <li>No products found.</li>
        @endforelse
    </ul>

    <script>
        function handleSubmit(e){
            e.preventDefault();

            console.log('e',e)
        }

        function handleReset(){
            window.location.href = "{{url('/products')}}"
        }
    </script>
</body>
</html>