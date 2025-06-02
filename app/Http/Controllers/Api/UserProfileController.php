<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    // Get user profile
    public function show()
    {
        $user = Auth::user();

        // Get user statistics using DB queries
        $stats = [
            'total_recipes' => Recipe::where('user_id', $user->id)->count(),
            'total_views' => Recipe::where('user_id', $user->id)->sum('views') ?? 0,
            'average_rating' => round(Recipe::where('user_id', $user->id)->avg('rating') ?? 0, 1),
            'account_created' => $user->created_at->format('M d, Y')
        ];

        $avatarUrl = $user->avatar_path
            ? asset('storage/' . $user->avatar_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f97316&color=fff&size=200';

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats,
                'avatar_url' => $avatarUrl
            ]
        ]);
    }

    //  user profile using DB
public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . Auth::id(),
        'phone' => 'nullable|string|max:20',
        'bio' => 'nullable|string|max:500',
        'location' => 'nullable|string|max:100'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $userId = Auth::id();
    $updateData = $request->only(['name', 'email', 'phone', 'bio', 'location']);

    // Use DB update instead of Eloquent
    DB::table('users')->where('id', $userId)->update($updateData);

    // Get updated user - FIXED LINE
    $user = DB::table('users')->where('id', $userId)->first();

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' => $user
    ]);
}


    // Upload avatar
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image file',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $userId = $user->id;

        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // Store new avatar
        $file = $request->file('avatar');
        $filename = 'user_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        // Update using DB
        DB::table('users')->where('id', $userId)->update(['avatar_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar_url' => asset('storage/' . $path)
            ]
        ]);
    }

    // Remove avatar
    public function removeAvatar()
    {
        $user = Auth::user();
        $userId = $user->id;

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);

            // Update using DB
            DB::table('users')->where('id', $userId)->update(['avatar_path' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar removed successfully'
        ]);
    }

    // Change password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password using DB 
        $userId = $user->id;
        $newPassword = Hash::make($request->new_password);

        DB::table('users')->where('id', $userId)->update(['password' => $newPassword]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
