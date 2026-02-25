<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    protected UserProfileService $userProfileService;

    public function __construct(UserProfileService $userProfileService)
    {
        $this->userProfileService = $userProfileService;
    }

    // Get user profile
    public function show()
    {
        $data = $this->userProfileService->getProfileData(Auth::user());

        return response()->json([
            'success' => true,
            'data' => $data
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

        $user = $this->userProfileService->updateProfile(
            Auth::id(), 
            $request->only(['name', 'email', 'phone', 'bio', 'location'])
        );

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

        $avatarUrl = $this->userProfileService->uploadAvatar(Auth::user(), $request->file('avatar'));

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar_url' => $avatarUrl
            ]
        ]);
    }

    // Remove avatar
    public function removeAvatar()
    {
        $this->userProfileService->removeAvatar(Auth::user());

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

        try {
            $this->userProfileService->changePassword(
                Auth::user(), 
                $request->current_password, 
                $request->new_password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }
    }
}
