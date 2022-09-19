<?php

namespace App\Policies;

use App\Models\LanguageLine;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LanguageLinePolicy
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
     * @param \App\Models\LanguageLine $languageLine
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function view(User $user, LanguageLine $languageLine)
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
     * @param \App\Models\LanguageLine $languageLine
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function update(User $user, LanguageLine $languageLine)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\LanguageLine $languageLine
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function delete(User $user, LanguageLine $languageLine)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\LanguageLine $languageLine
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function restore(User $user, LanguageLine $languageLine)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User   $user
     * @param \App\Models\LanguageLine $languageLine
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function forceDelete(User $user, LanguageLine $languageLine)
    {
        return false;
    }
}
