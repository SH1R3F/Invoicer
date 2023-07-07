<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            [
                'id'       => $this->id,
                'fullName' => $this->name,
                'name'     => $this->name,
                'email'    => $this->email,
                'avatar'   => $this->avatar ? Storage::url($this->avatar) : asset(User::DEFAULT_PICTURE),
                'image'    => $this->avatar ? Storage::url($this->avatar) : asset(User::DEFAULT_PICTURE),
                'role'     => $this->roles->first()?->name,
                'role_id'  => $this->roles->first()?->id,
            ]
        );
    }
}
