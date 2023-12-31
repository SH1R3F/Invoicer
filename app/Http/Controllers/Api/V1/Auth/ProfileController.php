<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\Auth\AccountSettings\UpdateProfileRequest;
use App\Http\Requests\Auth\AccountSettings\UpdatePasswordRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get Logged In User
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'userData' => new UserResource($user),
            'userAbilities' => PermissionResource::collection($user->getPermissionsViaRoles()),
        ]);
    }

    /**
     * Update Logged In User profile
     */
    public function profile(UpdateProfileRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Upload avatar
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store("users/{$request->user()->id}");
            if ($request->user()->avatar) Storage::delete($request->user()->avatar);
        }

        $request->user()->update($data);

        return response()->json([
            'message'  => __('Profile updated successfully'),
            'userData' => new UserResource($request->user())
        ]);
    }

    /**
     * Update Logged In User password
     */
    public function password(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update(['password' => $request->new_password]);

        return response()->json([
            'message'  => __('Password updated successfully'),
        ]);
    }

    /**
     * Update Logged In User profile
     */
    public function deactive(Request $request): JsonResponse
    {
        $request->user()->delete();

        return response()->json([
            'message'  => __('User deleted successfully'),
        ]);
    }
}
