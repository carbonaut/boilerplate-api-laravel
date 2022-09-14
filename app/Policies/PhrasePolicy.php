<?php

namespace App\Policies;

use App\Models\Phrase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhrasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Phrase $phrase
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function view(User $user, Phrase $phrase)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Phrase $phrase
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function update(User $user, Phrase $phrase)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Phrase $phrase
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Phrase $phrase)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Phrase $phrase
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function restore(User $user, Phrase $phrase)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\Phrase $phrase
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function forceDelete(User $user, Phrase $phrase)
    {
        return false;
    }
}
