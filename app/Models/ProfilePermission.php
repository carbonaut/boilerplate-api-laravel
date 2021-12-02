<?php

namespace App\Models;

use App\Support\Helpers;
use Illuminate\Support\Str;

class ProfilePermission extends BaseModel
{
    //======================================================================
    // APPENDED ATTRIBUTES
    //======================================================================

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'model_name',
        'permission_name',
    ];

    /**
     * Get model name from the policy name.
     *
     * @return int
     */
    public function getModelNameAttribute()
    {
        return preg_replace('/Policy$/', '', $this->policy);
    }

    /**
     * Get model name from the policy name.
     *
     * @return int
     */
    public function getPermissionNameAttribute()
    {
        return preg_replace('/Policy$/', '', $this->policy) . '\\' . $this->function;
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the profile that owns the profile permission.
     */
    public function profile()
    {
        return $this->belongsTo('\App\Models\Profile');
    }

    //======================================================================
    // STATIC METHODS
    //======================================================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($profile_permission) {
            if (strpos($profile_permission->policy, '\\') !== false) {
                $permission = explode('\\', $profile_permission->policy);

                $profile_permission->policy = $permission[0] . 'Policy';
                $profile_permission->function = $permission[1];

                // Check for uniqueness
                $existing = ProfilePermission::where('policy', $profile_permission->policy)
                    ->where('function', $profile_permission->function)
                    ->where('profile_id', $profile_permission->profile_id)
                    ->first();

                if ($existing !== null && $existing->id !== $profile_permission->id) {
                    throw new \Exception(__('validation.unique', ['attribute' => 'permission']));
                }
            }
        });
    }

    /*
     * Returns all methods from all policies.
     *
     * @return array array
     */
    public static function getAllProfilePermissions()
    {
        $profile_permissions = collect();

        // Get all classes from the policies directory
        Helpers::getClasses(app_path() . '/Policies')
            ->each(function ($class) use ($profile_permissions) {
                // Get all methods for each class
                collect($class->getMethods())
                    // Reject uninteresting methods
                    ->reject(function ($method) {
                        return Str::contains($method->name, ['allow', 'deny']);
                    })
                    ->each(function ($method) use ($class, $profile_permissions) {
                        $profilePermission = new ProfilePermission();
                        $profilePermission->policy = $class->getShortName();
                        $profilePermission->function = $method->name;

                        $profile_permissions->push($profilePermission);
                    });
            });

        return $profile_permissions->sortBy('permission_name');
    }
}
