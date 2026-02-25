<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user and request OTP.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->otpService->requestForRegistration($user);

        return $user;
    }

    /**
     * Authenticate user and request OTP.
     */
    public function login(array $credentials): User
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        $this->otpService->requestForLogin($user);

        return $user;
    }

    /**
     * Logout user by deleting current token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
