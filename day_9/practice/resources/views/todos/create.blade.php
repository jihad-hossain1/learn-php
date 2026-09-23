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
