<?php

namespace App\Policies;

use App\Models\User;

class ApplicationPolicy
{
    /**
     * Determine whether the user can enable/disable maintenance mode.
     *
     * @param null|User $user
     *
     * @return bool
     */
    public function toggleMaintenance(?User $user): bool
    {
        if ($user instanceof User) {
            return $user->email === 'hello@carbonaut.io';
        }

        return false;
    }
}
