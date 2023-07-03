<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;

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
}
