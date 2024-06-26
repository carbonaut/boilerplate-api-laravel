<?php

namespace App\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'email_verified',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
        'email_verified_at',
        'email_verification_code',
        'email_verification_code_expires_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'                  => 'datetime',
        'password'                           => 'hashed',
        'email_verification_code_expires_at' => 'datetime',
    ];

    /**
     * Interact with the user's email_verified.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function emailVerified(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->email_verified_at !== null,
        );
    }

    /**
     * Interact with the user's email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string>
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower(strval($value)),
        );
    }

    // ======================================================================
    // RELATIONSHIPS
    // ======================================================================

    /**
     * Get the devices owned by the user.
     *
     * @return HasMany<Device>
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    // ======================================================================
    // METHODS
    // ======================================================================

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale(): string
    {
        return $this->language;
    }
}
