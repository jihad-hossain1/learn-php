<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * Inject the TodoService (which depends on the Repository Interface).
     */
    public function __construct(
        private TodoService $todoService
    ) {}

    /**
     * Display a listing of todos with filtering and statistics.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = [
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'search' => $request->query('search'),
        ];

        $todos = $this->todoService->listTodos($filters);
        $statistics = $this->todoService->getStatistics();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $todos,
                'meta' => [
                    'statistics' => $statistics,
                    'filters' => $filters,
                ],
            ]);
        }

        return view('todos.index', [
            'todos' => $todos,
            'statistics' => $statistics,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new todo.
     */
    public function create(): View
    {
        return view('todos.create');
    }

    /**
     * Store a newly created todo.
     * Flow: Form Request -> Controller -> DTO -> Service -> Repo -> Eloquent -> DB
     */
    public function store(StoreTodoRequest $request): RedirectResponse|JsonResponse
    {
        // 1. Transform validated Form Request to DTO
        $dto = $request->toDTO();

        // 2. Delegate to Service layer
        $todo = $this->todoService->createTodo($dto);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Todo created successfully',
                'data' => $todo,
            ], 201);
        }

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    /**
     * Display the specified todo.
     */
    public function show(Request $request, int $id): View|JsonResponse
    {
        $todo = $this->todoService->getTodo($id);

        if ($request->wantsJson()) {
            return response()->json(['data' => $todo]);
        }

        return view('todos.show', ['todo' => $todo]);
    }

    /**
     * Show the form for editing the specified todo.
     */
    public function edit(int $id): View
    {
        $todo = $this->todoService->getTodo($id);

        return view('todos.edit', ['todo' => $todo]);
    }

    /**
     * Update the specified todo in storage.
     * Flow: Form Request -> Controller -> DTO -> Service -> Repo -> Eloquent -> DB
     */
    public function update(UpdateTodoRequest $request, int $id): RedirectResponse|JsonResponse
    {
        // 1. Transform validated Form Request to DTO
        $dto = $request->toDTO();

        // 2. Delegate to Service layer
        $todo = $this->todoService->updateTodo($id, $dto);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Todo updated successfully',
                'data' => $todo,
            ]);
        }

        return redirect()
            ->route('todos.show', $todo->id)
            ->with('success', 'Todo updated successfully!');
    }

    /**
     * Toggle completion status of a todo.
     */
    public function toggle(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $todo = $this->todoService->toggleTodoStatus($id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Todo status toggled',
                'data' => $todo,
            ]);
        }

        return back()->with('success', 'Todo status updated!');
    }

    /**
     * Remove the specified todo from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->todoService->deleteTodo($id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Todo deleted successfully',
            ]);
        }

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo removed successfully!');
    }
}
