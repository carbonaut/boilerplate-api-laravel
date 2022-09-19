<?php

namespace App\Enums;

enum LanguageLineGroup: string
{
    use EnumTrait;

    case Api = 'api';
    case App = 'app';
    case Email = 'email';

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
            LanguageLineGroup::Api   => 'Language-lines used in the API',
            LanguageLineGroup::App   => 'Language-lines used in the App',
            LanguageLineGroup::Email => 'Language-lines used in emails',
        };
    }
}
