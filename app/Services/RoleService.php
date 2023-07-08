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
        $role->syncPermissions($this->getPermissionNamesArray($data['permissions']));

        return $role;
    }

    /**
     * Update existing role & Sync new permissions
     */
    public function update($data, Role $role): void
    {
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($this->getPermissionNamesArray($data['permissions']));
    }


    /**
     * Prepare permissions
     */
    public function getPermissionNamesArray(array $perms): array
    {
        $permissions = array_reduce($perms, fn ($carry, $permission) => $carry + $permission, []);
        $permissions =  collect($permissions)
            ->filter(fn ($permission) => is_bool($permission) && $permission == true)
            ->keys()
            ->toArray();

        return $permissions;
    }
}
