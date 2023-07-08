<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $roles = Role::all();
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request, RoleService $service): JsonResponse
    {
        $role = $service->store($request->validated());

        return response()->json([
            'message' => __('Role created successfully'),
            'role'    => new RoleResource($role)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role, RoleService $service): JsonResponse
    {
        $service->update($request->validated(), $role);

        return response()->json([
            'message' => __('Role updated successfully'),
            'role'    => new RoleResource($role)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([
            'message' => __('Role deleted successfully')
        ]);
    }
}
