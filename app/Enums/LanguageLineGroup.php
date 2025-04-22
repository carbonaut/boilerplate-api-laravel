<?php

namespace App\Enums;

enum LanguageLineGroup: string
{
    use EnumTrait;

    case Api = 'api';
    case App = 'app';
    case Notifications = 'notifications';

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
            LanguageLineGroup::Api           => 'Language-lines used in the API',
            LanguageLineGroup::App           => 'Language-lines used in the App',
            LanguageLineGroup::Notifications => 'Language-lines used in notifications',
        };
    }
}
