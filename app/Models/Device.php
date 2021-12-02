<?php

namespace App\Models;

class Device extends BaseModel
{
    //======================================================================
    // FILLABLE ATTRIBUTES
    //======================================================================
    protected $fillable = [
        'uuid',
        'platform',
        'user_id',
    ];

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
        'device_id',
    ];

    /**
     * Get the device id for the device.
     *
     * @return int
     */
    public function getDeviceIdAttribute()
    {
        return $this->id;
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the client of the voucher.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
