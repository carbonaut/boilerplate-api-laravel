<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can enable/disable maintenance mode.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function maintenance(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can view the Nova Admin Panel.
     *
     * @param User $user
     *
     * @return bool
     */
    public function viewNova(User $user)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can view all users.
     *
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function view(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can create a user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function update(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can access the hotline.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function hotline(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can see appointments in a given location.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function appointments(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can perform the triage.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function triage(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can perform the exam.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function exam(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can access the results.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function results(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }

    /**
     * Determine whether the user can access the reports.
     *
     * @param User $user
     * @param User $iterator
     *
     * @return bool
     */
    public function reports(User $user, User $iterator)
    {
        return $user->getPermission(class_basename(__CLASS__), __FUNCTION__) !== null;
    }
}
