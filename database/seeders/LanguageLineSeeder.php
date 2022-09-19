<?php

namespace Database\Seeders;

use App\Services\LanguageLineService;
use Illuminate\Database\Seeder;

class LanguageLineSeeder extends Seeder
{
    /**
     * Array of language lines to be created.
     *
     * @var array
     */
    private $languageLines = [
        'api' => [
            'ERROR.SOMETHING_WENT_WRONG' => [
                'en'    => 'Something went wrong! Try again later.',
                'pt-BR' => 'Algo deu errado! Tente novamente mais tarde.',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LanguageLineService::createLanguageLines($this->languageLines);
    }
}
