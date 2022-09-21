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
                'pt_BR' => 'Algo deu errado! Tente novamente mais tarde.',
            ],
            'ERROR.CURRENT_PASSWORD_DOES_NOT_MATCH' => [
                'en'    => 'The provided password does not match your current password.',
                'pt_BR' => 'A senha informada não corresponde à sua senha atual.',
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
