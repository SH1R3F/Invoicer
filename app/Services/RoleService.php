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

        $permissions = collect($data['permissions'])
            ->flatten()
            ->filter(fn ($permission) => is_bool($permission) && $permission == true)
            ->keys()
            ->all();

        $role->syncPermissions($permissions);

        return $role;
    }
}
