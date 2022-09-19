<?php

namespace App\Models;

use App\Enums\LanguageLineGroup;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine as SpatieLanguageLine;

class LanguageLine extends SpatieLanguageLine
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
        'group' => LanguageLineGroup::class,
        'text'  => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'group',
        'key',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public $translatable = ['text'];

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
        return "{$this->group?->value}.{$this->key}";
    }

    /**
     * This method had to be overwritten so we can use ->group as a LanguageLineGroup enum.
     *
     * @return void
     */
    public function flushGroupCache()
    {
        foreach ($this->getTranslatedLocales() as $locale) {
            Cache::forget(static::getCacheKey($this->group->value, $locale));
        }
    }
}
