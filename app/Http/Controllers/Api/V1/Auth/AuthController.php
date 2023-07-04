<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Log users in
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = request()->user();

        return response()->json([
            'accessToken' => $user->createToken('auth')->plainTextToken,
            'userData' => new UserResource($user),
            'userAbilities' => PermissionResource::collection($user->getPermissionsViaRoles()),
        ]);
    }

    /**
     * Register users
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $request->register();

        return response()->json([
            'accessToken' => $user->createToken('auth')->plainTextToken,
            'userData' => new UserResource($user),
            'userAbilities' => PermissionResource::collection($user->getPermissionsViaRoles()),
        ]);
    }

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

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $request->user()->update($data);

        return response()->json([
            'userData' => new UserResource($request->user())
        ]);
    }

    /**
     * Log the current user out
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
