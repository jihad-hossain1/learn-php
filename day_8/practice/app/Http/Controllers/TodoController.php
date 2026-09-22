<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Data\Todo\CreateTodoData;
use App\Services\TodoService;

class TodoController extends Controller
{
    public function __construct(
        readonly TodoService $todoService
    ) {}
    
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Todo list'
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $data = CreateTodoData::fromRequest($request);

        $todo = $this->todoService->create($data);

        return response()->json($todo, 201);
    }
}
