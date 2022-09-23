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
            'ERROR.EMAIL.NOT_VERIFIED' => [
                'en'    => 'Please verify your email address.',
                'pt_BR' => 'Por favor confirme seu endereço de email.',
            ],
            'ERROR.MAINTENANCE' => [
                'en'    => 'We\'re under maintanance and will be back shortly.',
                'pt_BR' => 'Estamos em manutenção e voltamos em breve.',
            ],
            'ERROR.AUTH.INVALID_CREDENTIALS' => [
                'en'    => 'Check your credentials and try again.',
                'pt_BR' => 'Verifique suas credenciais e tente novamente.',
            ],
            'ERROR.AUTH.UNAUTHENTICATED' => [
                'en'    => 'You are not authenticated. Login and try again.',
                'pt_BR' => 'Você nao está autenticado. Faça login e tente novamente.',
            ],
            'ERROR.AUTH.UNAUTHORIZED' => [
                'en'    => 'You don\'t have permissions to access this resource.',
                'pt_BR' => 'Você não tem permissão para acessar esse recurso.',
            ],
            'ERROR.SOMETHING_WENT_WRONG' => [
                'en'    => 'Something went wrong! Try again later.',
                'pt_BR' => 'Algo deu errado! Tente novamente mais tarde.',
            ],
            'ERROR.MODEL_NOT_FOUND' => [
                'en'    => 'We couldn\'t find what you are looking for.',
                'pt_BR' => 'Não encontramos o que você está procurando.',
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
