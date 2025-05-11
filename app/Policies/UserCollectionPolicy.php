<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\UserCollection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserCollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Example: Allow admins to view any, or disallow for now if not needed.
        // return $user->isAdmin(); 
        return true; // Or based on specific roles/permissions
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, UserCollection $userCollection): bool
    {
        // Allow viewing if the collection is public
        if ($userCollection->is_public) {
            return true;
        }
        // Allow viewing if the user is authenticated and owns the collection
        return $user && $user->id === $userCollection->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a collection (can be restricted by roles/permissions later)
        return true; 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserCollection $userCollection): bool
    {
        return $user->id === $userCollection->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserCollection $userCollection): bool
    {
        return $user->id === $userCollection->user_id;
    }

    /**
     * Determine whether the user can set the model as default.
     */
    public function setDefault(User $user, UserCollection $userCollection): bool
    {
        return $user->id === $userCollection->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserCollection $userCollection): bool
    {
        // Not used for now, implement if soft deletes are used for UserCollection
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserCollection $userCollection): bool
    {
        // Not used for now, implement if soft deletes are used for UserCollection
        return false;
    }
}
