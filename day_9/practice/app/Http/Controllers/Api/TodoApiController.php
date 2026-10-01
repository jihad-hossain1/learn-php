<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoApiController extends Controller
{
    public function __construct(
        private TodoService $todoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'search' => $request->query('search'),
        ];

        $todos = $this->todoService->listTodos($filters);
        $statistics = $this->todoService->getStatistics();

        return response()->json([
            'status' => 'success',
            'data' => $todos,
            'meta' => [
                'statistics' => $statistics,
                'filters' => $filters,
            ],
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $todo = $this->todoService->createTodo($dto);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo created successfully',
            'data' => $todo,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $todo = $this->todoService->getTodo($id);

        return response()->json([
            'status' => 'success',
            'data' => $todo,
        ]);
    }

    public function update(UpdateTodoRequest $request, int $id): JsonResponse
    {
        $dto = $request->toDTO();
        $todo = $this->todoService->updateTodo($id, $dto);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'data' => $todo,
        ]);
    }

    public function toggle(int $id): JsonResponse
    {
        $todo = $this->todoService->toggleTodoStatus($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo status toggled successfully',
            'data' => $todo,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->todoService->deleteTodo($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
        ]);
    }
}
