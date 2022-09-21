<?php

namespace App\Enums;

enum Language: string
{
    use EnumTrait;

    case English = 'en';
    case BrazilianPortuguese = 'pt_BR';

    /**
     * Custom labels defined for each enum case.
     *
     * @param self $value
     *
     * @return string
     */
    public static function getLabel(self $value): string
    {
        return match ($value) {
            Language::English             => 'English',
            Language::BrazilianPortuguese => 'Português do Brasil',
        };
    }
}
