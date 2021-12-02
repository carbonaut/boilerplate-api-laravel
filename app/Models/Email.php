<?php

namespace App\Models;

use App\Casts\Mailable;
use App\Jobs\ProcessEmail;
use Carbon\Carbon;

class Email extends BaseModel
{
    //======================================================================
    // CASTS ATTRIBUTES
    //======================================================================

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mailable'      => Mailable::class,
        'scheduled_for' => 'datetime',
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
        'email_id',
    ];

    /**
     * Get the id for the email notification.
     *
     * @return int
     */
    public function getEmailIdAttribute()
    {
        return $this->id;
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the models that owns the emailable.
     */
    public function emailable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user from which the email is for.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the user which sent the email.
     */
    public function sent_by()
    {
        return $this->belongsTo('App\Models\User', 'sent_by');
    }

    //======================================================================
    // STATIC METHODS
    //======================================================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // On creating entry, set default values if they are null
        static::creating(function ($email) {
            if ($email->to === null && $email->user !== null) {
                $email->to = $email->user->email;
            }

            if ($email->status === null) {
                $email->status = 'scheduled';
            }

            if ($email->scheduled_for === null) {
                $email->scheduled_for = Carbon::now();
            }
        });

        // On entry created, only queue the ones that are scheduled
        static::created(function ($email) {
            if ($email->status === 'scheduled') {
                ProcessEmail::dispatch($email)->onQueue('emails')->delay($email->scheduled_for);
            }
        });
    }
}
