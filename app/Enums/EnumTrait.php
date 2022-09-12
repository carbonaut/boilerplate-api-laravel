<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

trait EnumTrait
{
    use InvokableCases;
    use Names;
    use Values;
    use Options;

    /**
     * Returns an associative array of [value => label|name] of all enum cases.
     * 
     * If a label is not defined, the enum name is used instead.
     *
     * @return array<string>
     */
    public static function asSelectArrayUsingLabels(): array
    {
        $array = [];

        foreach (static::cases() as $case) {
            $array[$case->value] = $case->label() ?? $case->name;
        }

        return $array;
    }

    /**
     * Override this method on your Enum to add labels to your enum cases.
     *
     * @param self $value
     *
     * @return null|string
     */
    public static function getLabel(self $value): ?string
    {
        return null;
    }

    /**
     * Returns the label defined for each enum case.
     *
     * @return string
     */
    public function label(): ?string
    {
        return static::getLabel($this);
    }
}
