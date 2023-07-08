<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{

    /**
     * Store new role & Sync permissions
     */
    public function store($data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        $permissions = array_reduce($data['permissions'], fn ($carry, $permission) => $carry + $permission, []);
        $permissions = collect($permissions)
            ->filter(fn ($permission) => is_bool($permission) && $permission == true)
            ->keys()
            ->toArray();

        $role->syncPermissions($permissions);

        return $role;
    }
}
