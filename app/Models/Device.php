<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends BaseModel
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_version',
        'is_active',
        'is_virtual',
        'manufacturer',
        'model',
        'name',
        'operating_system',
        'os_version',
        'platform',
        'push_token',
        'uuid',
        'web_view_version',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    // ======================================================================
    // RELATIONSHIPS
    // ======================================================================

    /**
     * Gets the user that owns the device.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
