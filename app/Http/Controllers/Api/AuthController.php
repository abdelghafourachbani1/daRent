<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller {

    protected $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function register(Request $request) {

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:tenant,owner',
        ]);
        $user = $this->authService->register($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = $this->authService->login($data);
        if (!$user) {
            return response()->json(
                ['message' => 'Invalid credentials'],401
            );
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'logged out']);
    }

    public function me(Request $request) {
        return response()->json($request->user());
    }
    
}