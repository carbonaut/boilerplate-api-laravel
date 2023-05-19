<?php

namespace App\Enums;

trait EnumTrait
{
    /**
     * Return an associative array of [value => label] of all enum cases.
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
     * Return the label for the enum case,
     * if the label is not defined the case's name is returned.
     *
     * @return string
     */
    public function label(): string
    {
        return static::getLabel($this) ?: $this->name;
    }
}
