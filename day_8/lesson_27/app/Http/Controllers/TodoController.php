<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTodoRequest;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Todo list'
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse 
    {
        // $request->validate()
    }
}
