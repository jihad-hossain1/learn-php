<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;

class AuthController extends Controller
{

    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $register = $this->authService->register($dto);

        return response()->json($register);
    }

    public function verify(): void
    {
        //
    }

    public function login(): void
    {
        //
    }

    public function forgotPassword(): void
    {
        //
    }
}
