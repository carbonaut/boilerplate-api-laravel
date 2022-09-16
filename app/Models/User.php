<?php

namespace App\Models;

use App\Http\Traits\NestedRelations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use NestedRelations;

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
        'user_id',
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
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
        'email_verification_code_expires_at' => 'datetime',
    ];

    /**
     * Interact with the user's email_verified.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function emailVerified(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->email_verified_at !== null,
        );
    }

    /**
     * Interact with the user's user_id.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function userId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->id,
        );
    }

    //======================================================================
    // RELATIONSHIPS
    //======================================================================

    /**
     * Get the profile that owns the user.
     */
    public function profile()
    {
        return $this->belongsTo('\App\Models\Profile');
    }

    /**
     * Get the language that owns the user.
     */
    public function language()
    {
        return $this->belongsTo('\App\Models\Language');
    }

    //======================================================================
    // METHODS
    //======================================================================

    /**
     * Get the given policy function permission for the user.
     *
     * @param string $policy
     * @param string $function
     *
     * @return null|bool
     */
    public function getPermission($policy, $function)
    {
        if (!$this->relationLoaded('profile')) {
            throw new \Exception('Profile must be eager loaded before geting a permission');
        }

        // Deny when the user has no profile
        if ($this->profile === null) {
            return null;
        }

        // Search the policy function permission for the user
        return $this->profile->getPermission($policy, $function);
    }

    /**
     * Set the app locale using the user proper language.
     */
    public function setLocale()
    {
        if ($this->language_id !== null) {
            App::setLocale($this->language->locale);
        } else {
            App::setLocale(config('app.default_locale'));
        }
    }

    //======================================================================
    // STATIC METHODS
    //======================================================================

    /**
     * Revoke the user authentication token.
     *
     * @param null|Token $accessToken
     */
    protected static function revokeToken(Token $accessToken)
    {
        // Revoke the refresh token associated with the access token
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);

        // Revoke the access token itself
        $accessToken->revoke();
    }

    /**
     * Returns a verification code.
     *
     * @param int $digits
     *
     * @return int
     */
    public static function generateVerificationCode($digits = 4)
    {
        return rand(pow(10, $digits - 1), (pow(10, $digits) - 1));
    }
}
