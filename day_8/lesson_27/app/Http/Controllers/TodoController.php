<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Todo list'
        ]);
    }
}
