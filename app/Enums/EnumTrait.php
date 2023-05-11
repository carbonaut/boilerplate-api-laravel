<?php

namespace App\Enums;

trait EnumTrait
{
    /**
     * Return an associative array of [value => label|name] of all enum cases.
     *
     * If a label is not defined, the enum name is used instead.
     *
     * @return array<string, string>
     */
    public static function asSelect(): array
    {
        $array = [];

        foreach (static::cases() as $case) {
            $array[$case->value] = $case->label();
        }

        return $array;
    }

    /**
     * Return an all cases formatted as a data provider.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function asDataProvider(): array
    {
        $array = [];

        foreach (static::cases() as $case) {
            $array[$case->name] = [$case->value];
        }

        return $array;
    }

    /**
     * Return a random case of the enum.
     *
     * @return static
     */
    public static function randomCase(): static
    {
        $cases = static::cases();

        return $cases[array_rand($cases)];
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
     * Return the label defined for the enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return static::getLabel($this) ?: $this->name;
    }
}
