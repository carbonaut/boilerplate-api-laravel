<?php

namespace App\Models;

class Language extends BaseModel {
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
        'language_id',
    ];

    /**
     * Get the language id for the language.
     *
     * @return int
     */
    public function getLanguageIdAttribute() {
        return $this->id;
    }
}
