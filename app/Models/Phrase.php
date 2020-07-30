<?php

namespace App\Models;

use App;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Log;

class Phrase extends BaseModel implements TranslatableContract {
    use Translatable;

    //======================================================================
    // EAGER-LOADED RELATIONS
    //======================================================================
    protected $with = [
        'translations',
    ];

    //======================================================================
    // TRANSLATED ATTRIBUTES
    //======================================================================

    public $translatedAttributes = [
        'value',
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
        'phrase_id',
    ];

    /**
     * Get the phrase id for the phrase.
     *
     * @return int
     */
    public function getPhraseIdAttribute() {
        return $this->id;
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
    public static function getPhrase($key, $type, $replacement = null) {
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
