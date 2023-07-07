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

        $permissions = [];
        foreach ($data['permissions'] as $group) {
            $group = array_filter($group, fn ($p) => is_bool($p) && $p == true);
            $permissions = array_merge($permissions, $group);
        }

        $role->syncPermissions(array_keys($permissions));

        return $role;
    }
}
