<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{

    /**
     * Store new user & Sync role
     */
    public function store(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $role = Role::where('name', $data['role'])->first();
        $user->syncRoles($role);

        return $user;
    }

    /**
     * Update existing user & Sync new role
     */
    public function update($data, User $user): void
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $role = Role::where('name', $data['role'])->first();
        $user->syncRoles($role);
    }
}
