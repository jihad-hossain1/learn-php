<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Product</title>
</head>
<body>
    <h4>New Product Form</h4>
    
    <!-- Added an ID to the form -->
    <form id="productForm" action="{{url('/products')}}" method="POST">
        @csrf <!-- Required for Laravel POST requests -->
        
        <div>
            <label for="name">Name</label> <br/>
            <input type="text" name="name" id="name"/> 
        </div>
        <br/>
        <div>
            <label for="price">Price</label> <br/> 
            <input type="number" name="price" id="price"> 
        </div>
        <br/>
        <div>
            <label for="description">Description</label> <br/>
            <textarea name="description" id="description" ></textarea>
        </div>

        <div>
            <button type="submit"> Submit </button>
        </div>
    </form>

    <!-- JavaScript to prevent reload and handle AJAX submission -->
    <script>
        document.getElementById('productForm').addEventListener('submit', function(e) {
            // 1. Stop the default page reload
            e.preventDefault();

            // 2. Collect all form data
            let formData = new FormData(this);

            // 3. Send data asynchronously using Fetch API
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
                alert('Product added successfully without page reload!');
                
                // Optional: Reset the form after successful submission
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