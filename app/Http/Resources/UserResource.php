<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
                'avatar'   => asset($this->avatar ?? 'assets/img/user.png'),
                'role'     => $this->roles->first()?->name,
                'role_id'  => $this->roles->first()?->id,
            ]
        );
    }
}
