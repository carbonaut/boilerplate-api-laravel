<?php

namespace App\Enums;

enum PhraseType: string
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
            PhraseType::Api   => 'Phrases used in the API',
            PhraseType::App   => 'Phrases used in the App',
            PhraseType::Email => 'Phrases used in emails',
        };
    }
}
