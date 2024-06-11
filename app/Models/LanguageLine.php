<?php

namespace App\Models;

use App\Enums\LanguageLineGroup;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine as SpatieLanguageLine;

class LanguageLine extends SpatieLanguageLine
{
    use HasFactory;
    use HasUuids;

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
        'handle',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'group' => LanguageLineGroup::class,
            'text'  => 'array',
        ];
    }

    /**
     * Interact with the phrase's key.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string>
     */
    protected function key(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::of($value)->replace(' ', '_')->upper()->__toString(),
        );
    }

    /**
     * Return the handle of the phrase (type.KEY).
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function handle(): Attribute
    {
        return Attribute::get(
            fn () => "{$this->group->value}.{$this->key}"
        );
    }

    /**
     * Clear group's cache on all traslated locales.
     *
     * This method had to be overwritten to access group's value (`Enum::$value`).
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
