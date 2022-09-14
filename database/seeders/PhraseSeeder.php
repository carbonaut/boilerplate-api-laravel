<?php

namespace Database\Seeders;

use App\Services\PhraseService;
use Illuminate\Database\Seeder;

class PhraseSeeder extends Seeder
{
    /**
     * Array of phrases to be created.
     *
     * @var array
     */
    private $phrases = [
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
        PhraseService::createPhrases($this->phrases);
    }
}
