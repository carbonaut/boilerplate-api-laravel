<?php

namespace App\Models;

use App;
use App\Http\Traits\NestedRelations;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use NestedRelations;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    //======================================================================
    // FILLABLE ATTRIBUTES
    //======================================================================

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password',
        'remember_token',
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
        'user_id',
        'email_verified',
        'full_name',
    ];

    /**
     * Get the user id.
     *
     * @return int
     */
    public function getUserIdAttribute()
    {
        return $this->id;
    }

    /**
     * Returns if the user email was verified.
     *
     * @return bool
     */
    public function getEmailVerifiedAttribute()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Returns user full name (concat of first and last).
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->title !== null) {
            return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    //======================================================================
    // CAST ATTRIBUTES
    //======================================================================

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'                  => 'datetime',
        'email_verification_code_expires_at' => 'datetime',
    ];

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
