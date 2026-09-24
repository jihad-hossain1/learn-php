

<form
    {{-- method="POST"
    action="{{ route('todos.store') }}" --}}
>
    @csrf


    <div>
        <label for="title">
            Title
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
        >

        @error('name')
            <p>{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">
        Create Todo
    </button>
</form>
