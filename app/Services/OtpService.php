<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function requestForRegistration(User $user): void
    {
        $this->generateAndSend($user->email, 'register', $user->id);
    }

    public function requestForLogin(User $user): void
    {
        $this->generateAndSend($user->email, 'login', $user->id);
    }

    public function resend(string $email, string $type): void
    {
        if (!in_array($type, ['register', 'login'], true)) {
            throw ValidationException::withMessages([
                'type' => ['Invalid OTP type.'],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No user found with this email address.'],
            ]);
        }

        $this->generateAndSend($email, $type, $user->id);
    }

    public function verify(string $email, string $otp, string $type): User
    {
        /** @var OtpVerification|null $record */
        $record = OtpVerification::where('email', $email)
            ->where('type', $type)
            ->latest()
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'otp' => ['No active OTP found for this email. Please request a new code.'],
            ]);
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            $record->delete();

            throw ValidationException::withMessages([
                'otp' => ['The OTP has expired. Please request a new code.'],
            ]);
        }

        if ($record->attempts >= 5) {
            $record->delete();

            throw ValidationException::withMessages([
                'otp' => ['Maximum OTP attempts exceeded. Please request a new code.'],
            ]);
        }

        if (! Hash::check($otp, $record->otp)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is incorrect.'],
            ]);
        }

        $record->delete();

        $user = $record->user_id
            ? User::findOrFail($record->user_id)
            : User::where('email', $email)->firstOrFail();

        if ($type === 'register' && $user->email_verified_at === null) {
            $user->forceFill([
                'email_verified_at' => Carbon::now(),
            ])->save();
        }

        return $user;
    }

    protected function generateAndSend(string $email, string $type, ?int $userId = null): void
    {
        $this->cleanupExpiredOtps();

        OtpVerification::where('email', $email)
            ->where('type', $type)
            ->delete();

        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'user_id' => $userId,
            'email' => $email,
            'otp' => Hash::make($plainOtp),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
        ]);

        Mail::to($email)->send(new OtpMail($plainOtp, $type));
    }

    protected function cleanupExpiredOtps(): void
    {
        OtpVerification::where('expires_at', '<', Carbon::now())->delete();
    }
}

