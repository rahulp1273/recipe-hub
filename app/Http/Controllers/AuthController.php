<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Register new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $this->authService->register($validated);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify the OTP sent to your email.',
            'requires_otp' => true,
            'email' => $user->email,
            'type' => 'register',
        ], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->authService->login($validated);

        return response()->json([
            'success' => true,
            'message' => 'Login successful. Please verify the OTP sent to your email.',
            'requires_otp' => true,
            'email' => $user->email,
            'type' => 'login',
        ]);
    }

    // Logout user
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // Get authenticated user
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }
}
