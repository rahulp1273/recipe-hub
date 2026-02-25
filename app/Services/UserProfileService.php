<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserProfileService
{
    /**
     * Get user profile and statistics.
     */
    public function getProfileData(User $user): array
    {
        $stats = [
            'total_recipes' => Recipe::where('user_id', $user->id)->count(),
            'total_views' => Recipe::where('user_id', $user->id)->sum('views') ?? 0,
            'average_rating' => round(Recipe::where('user_id', $user->id)->avg('rating') ?? 0, 1),
            'account_created' => $user->created_at->format('M d, Y')
        ];

        $avatarUrl = $user->avatar_path
            ? asset('storage/' . $user->avatar_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f97316&color=fff&size=200';

        return [
            'user' => $user,
            'stats' => $stats,
            'avatar_url' => $avatarUrl
        ];
    }

    /**
     * Update user profile data.
     */
    public function updateProfile(int $userId, array $data): \stdClass
    {
        DB::table('users')->where('id', $userId)->update($data);
        return DB::table('users')->where('id', $userId)->first();
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(User $user, $file): string
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        DB::table('users')->where('id', $user->id)->update(['avatar_path' => $path]);

        return asset('storage/' . $path);
    }

    /**
     * Remove user avatar.
     */
    public function removeAvatar(User $user): void
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            DB::table('users')->where('id', $user->id)->update(['avatar_path' => null]);
        }
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect']
            ]);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
