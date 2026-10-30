<?php

namespace App\Http\Controllers;

use App\Services\CodeService;
use App\Http\Requests\CodeCreateRequest;

class CodeController extends Controller
{
    public function __construct(private CodeService $codeService) {}

    public function index()
    {
        $codes = $this->codeService->getAll();

        return view('lookups.index', [
            'lookups' => $codes
        ]);
    }

    public function show() {}

    public function store(CodeCreateRequest $request)
    {
        $result = $this->codeService->store($request->toDTO());

        return response()->json([
            'data' => $result
        ]);
    }
}
