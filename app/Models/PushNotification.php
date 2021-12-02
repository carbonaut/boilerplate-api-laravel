<?php

namespace App\Models;

use App\Jobs\ProcessPushNotification;
use Illuminate\Support\Carbon;

class PushNotification extends BaseModel
{
    //======================================================================
    // CAST ATTRIBUTES
    //======================================================================

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sent_at'        => 'datetime',
        'failed_at'      => 'datetime',
        'opened_at'      => 'datetime',
        'scheduled_for'  => 'datetime',
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
        'push_notification_id',
    ];

    /**
     * Get the id for the push notification.
     *
     * @return int
     */
    public function getPushNotificationIdAttribute()
    {
        return $this->id;
    }

    protected static function booted()
    {
        // On creating entry, set default values if they are null
        static::creating(function ($push) {
            if ($push->status === null) {
                $push->status = 'scheduled';
            }

            if ($push->scheduled_for === null) {
                $push->scheduled_for = Carbon::now();
            }
        });

        // On entry created, only queue the ones that are scheduled
        static::created(function ($push) {
            if ($push->status === 'scheduled') {
                ProcessPushNotification::dispatch($push)->onQueue('push_notifications')->delay($push->scheduled_for);
            }
        });
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the device that will receive the push notification.
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
