<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $authenticable
     *
     * @return bool
     */
    public function viewAny(User $authenticable): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $authenticable
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function view(User $authenticable, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $authenticable
     *
     * @return bool
     */
    public function create(User $authenticable): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $authenticable
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function update(User $authenticable, User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $authenticable
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function delete(User $authenticable, User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $authenticable
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function restore(User $authenticable, User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $authenticable
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function forceDelete(User $authenticable, User $user): bool
    {
        return false;
    }
}
