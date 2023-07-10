<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuotePolicy
{

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, $ability, mixed $quote)
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
        return $user->hasPermissionTo('Read quote');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('Read quote');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create quote');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('Update quote');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quote $quote): bool
    {
        return $user->hasPermissionTo('Delete quote');
    }
}
