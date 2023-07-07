<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'role'    => $this->name,
            'users'   => $this->users()->pluck('avatar')->map(fn ($avatar) => $avatar ? Storage::url($avatar) : asset(User::DEFAULT_PICTURE)),
            'details' => [
                'id'          => $this->id,
                'name'        => $this->name,
                'permissions' => $this->preparePermissions($this->permissions)
            ],
            'editable'  => $request->user()->can('update', $this->resource),
            'deletable' => $request->user()->can('delete', $this->resource),
        ];
    }

    /**
     * Prepare the permissions
     */
    public function preparePermissions(Collection $permissions): array
    {
        $names = [
            'role' => 'Roles',
            'user' => 'Users'
        ];
        $perms = $permissions->reduce(function ($carry, $permission) use ($names) {
            foreach ($names as $name => $value) {
                if (str_contains($permission->name, $name)) {
                    $carry[$value]['name'] = $value;
                    $carry[$value][$permission->name] = true;
                    break;
                }
            }
            return $carry;
        }, []);

        return array_values($perms);
    }
}
