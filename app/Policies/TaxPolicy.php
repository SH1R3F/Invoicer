<?php

namespace App\Policies;

use App\Models\Tax;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaxPolicy
{

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, $ability, mixed $tax)
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Read tax');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Read tax');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create tax');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Update tax');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Delete tax');
    }
}
