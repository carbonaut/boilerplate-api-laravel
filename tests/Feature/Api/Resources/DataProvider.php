<?php

namespace Tests\Feature\Api\Resources;

trait DataProvider
{
    /**
     * Available languages.
     *
     * @return array<string, array<int, array<int, array<string, string>>>>
     */
    public static function availableLanguages(): array
    {
        return [
            'all-languages' => [
                [
                    [
                        'value' => 'en',
                        'label' => 'English',
                    ],
                    [
                        'value' => 'pt_BR',
                        'label' => 'Português do Brasil',
                    ],
                ],
            ],
        ];
    }

    /**
     * Localized language line.
     *
     * @return array<string, array<string>>
     */
    public static function localizedLanguageLine(): array
    {
        return [
            'english' => [
                'en',
                'Example Text',
            ],
            'brazilian portuguese' => [
                'pt-BR',
                'Texto de Exemplo',
            ],
        ];
    }
}
