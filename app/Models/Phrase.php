<?php

namespace App\Models;

use App\Enums\PhraseType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Phrase extends BaseModel
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'handle',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => PhraseType::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'key',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public $translatable = ['value'];

    /**
     * Interact with the phrase's key.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function key(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->replace(' ', '_')->upper()->__toString(),
        );
    }

    /**
     * Returns the handle of the phrase (type.KEY).
     *
     * @return string
     */
    public function getHandleAttribute(): string
    {
        return "{$this->type?->value}.{$this->key}";
    }

    //======================================================================
    // STATIC PROPERTIES
    //======================================================================

    private static $phrases;
    private static $phrases_locale;

    //======================================================================
    // STATIC METHODS
    //======================================================================

    /**
     * Returns a phrase from a phrase pool. If the key isn't found, return the key itself.
     *
     * @param string     $key         phrase key to look for
     * @param string     $type        phrase type (like api, app, email, etc)
     * @param null|mixed $replacement replacement key and value to replace in phrase
     *
     * @return string phrase or key if phrase is not found in pool
     */
    public static function getPhrase($key, $type, $replacement = null)
    {
        if (self::$phrases === null || self::$phrases_locale !== App::getLocale()) {
            $phrases = Phrase::all();
            $p = [];

            foreach ($phrases as $phrase) {
                if (!isset($p[$phrase->type])) {
                    $p[$phrase->type] = [];
                }

                $p[$phrase->type][$phrase->key] = $phrase->value;
            }

            self::$phrases = $p;
            self::$phrases_locale = App::getLocale();
        }

        if (isset(self::$phrases[$type],self::$phrases[$type][$key])) {
            if ($replacement !== null) {
                return strtr(self::$phrases[$type][$key], $replacement);
            }

            return self::$phrases[$type][$key];
        }

        Log::warning('Trying to load a missing phrase!', ['phrase' => $type . '.' . $key]);

        return $type . '.' . $key;
    }
}
