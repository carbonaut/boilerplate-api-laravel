<?php

namespace App\Models;

class Profile extends BaseModel
{
    //======================================================================
    // HIDDEN ATTRIBUTES
    //======================================================================

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

    //======================================================================
    // APPENDED ATTRIBUTES
    //======================================================================

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_id',
    ];

    /**
     * Get the profile id for the profile.
     *
     * @return int
     */
    public function getProfileIdAttribute()
    {
        return $this->id;
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the users for the profile.
     */
    public function users()
    {
        return $this->hasMany('\App\Models\User');
    }

    /**
     * Get the profile permissions for the profile.
     */
    public function permissions()
    {
        return $this->hasMany('\App\Models\ProfilePermission');
    }

    //======================================================================
    // METHODS
    //======================================================================

    /**
     * Get the given policy function permission for the profile.
     *
     * @param string $policy
     * @param string $function
     *
     * @return null|bool
     */
    public function getPermission($policy, $function)
    {
        if (!$this->relationLoaded('permissions')) {
            throw new \Exception('Permissions must be eager loaded before geting a permission');
        }

        // Deny when the profile is inactive
        if (!$this->active) {
            return null;
        }

        // Search the policy function permission for the profile
        return $this->permissions->where('policy', $policy)->where('function', $function)->first();
    }
}
