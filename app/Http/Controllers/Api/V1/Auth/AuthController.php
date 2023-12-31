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

class AuthController extends Controller
{

    /**
     * Log users in
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

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
     * Log the current user out
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
