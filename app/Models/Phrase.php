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
}
