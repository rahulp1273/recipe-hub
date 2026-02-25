<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'type' => 'required|in:register,login',
        ]);

        $result = $this->otpService->verifyAndAuthenticate(
            $validated['email'],
            $validated['otp'],
            $validated['type'],
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'user' => $result['user'],
            'token' => $result['token'],
        ]);
    }

    public function resend(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:register,login',
        ]);

        $this->otpService->resend(
            $validated['email'],
            $validated['type'],
        );

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your email.',
        ]);
    }
}

