<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $this->updatePassword($data);
        $this->updateAvatar($data, $user);

        $user->update($data);
        $role = Role::where('name', $data['role'])->first();
        $user->syncRoles($role);
    }

    private function updatePassword(array &$data): void
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
    }

    private function updateAvatar(array &$data, User $user): void
    {
        // Upload avatar
        if ($data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $data['avatar']->store("users/{$user->id}");
            if ($user->avatar) Storage::delete($user->avatar);
        } else {
            unset($data['avatar']);
        }
    }
}
