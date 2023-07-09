<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $users = User::filter($request->only(['role']))
            ->search($request->q, ['name', 'email'])
            ->order($request->options['sortBy'] ?? [])
            ->paginate($request->options['itemsPerPage'] ?? 10, ['*'], 'page', $request->options['page'] ?? 1)
            ->withQueryString();

        return UserResource::collection($users)
            ->additional(['roles' => Role::pluck('name')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, UserService $service): JsonResponse
    {
        $user = $service->store($request->validated());

        return response()->json([
            'message' => __('User created successfully'),
            'user'    => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user'  => new UserResource($user),
            'roles' => Role::pluck('name')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user, UserService $service): JsonResponse
    {
        $service->update($request->validated(), $user);

        return response()->json([
            'message' => __('User updated successfully'),
            'user'    => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json([
            'message' => __('User deleted successfully')
        ]);
    }
}
