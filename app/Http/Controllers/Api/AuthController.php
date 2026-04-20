<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        [$token, $user] = $this->authService->register($validated);

        return $this->tokenResponse($token, $user, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $loginResult = $this->authService->login($credentials);

        if ($loginResult === null) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        [$token, $user] = $loginResult;

        return $this->tokenResponse($token, $user);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        return response()->json([
            'user' => auth('api')->user()->load('roles'),
        ]);
    }

    private function tokenResponse(string $token, User $user, int $status = 200)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user->load('roles'),
        ], $status);
    }
}
