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
            'ERROR.PASSWORD-RESET.INVALID-INPUT' => [
                'en'    => 'The provided email or token are not valid.',
                'pt_BR' => 'O token ou email informados não são válidos.',
            ],
            'ERROR.EMAIL.NOT_VERIFIED' => [
                'en'    => 'Please verify your email address.',
                'pt_BR' => 'Por favor confirme seu endereço de email.',
            ],
            'ERROR.EMAIL.ALREADY_VERIFIED' => [
                'en'    => 'This email was already verified.',
                'pt_BR' => 'O email já foi verificado.',
            ],
            'ERROR.EMAIL.VERIFICATION_CODE_EXPIRED' => [
                'en'    => 'The verification code has expired. A new code was sent to your email.',
                'pt_BR' => 'O código de verificação expirou. Um novo código foi enviado para o seu email.',
            ],
            'ERROR.EMAIL.VERIFICATION_CODE_MISMATCH' => [
                'en'    => 'The verification code is invalid.',
                'pt_BR' => 'O código de verificação é inválido.',
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
        'email' => [
            // Global Email Phrases
            'GLOBAL.INTRO-WITH-NAME' => [
                'en'    => 'Hi :name,',
                'pt_BR' => 'Olá :name,',
            ],
            'GLOBAL.INTRO-WITHOUT-NAME' => [
                'en'    => 'Hi,',
                'pt_BR' => 'Olá,',
            ],
            'GLOBAL.OUTRO-WITH-NAME' => [
                'en'    => 'Thanks,<br>:name',
                'pt_BR' => 'Obrigado,<br>:name,',
            ],
            // Mail\User\EmailVerification
            'USER.EMAIL-VERIFICATION.SUBJECT' => [
                'en'    => 'Welcome! Use :code to confirm your email address',
                'pt_BR' => 'Olá! Use :code para confirmar o seu endereço de email',
            ],
            'USER.EMAIL-VERIFICATION.CONTENT' => [
                'en'    => 'Use the following code to confirm your email address:',
                'pt_BR' => 'Use o código abaixo para confirmar o seu endereço de email:',
            ],
            // Mail\User\PasswordReset
            'USER.PASSWORD-RESET.SUBJECT' => [
                'en'    => 'Forgot your password?',
                'pt_BR' => 'Esqueceu sua senha?',
            ],
            'USER.PASSWORD-RESET.CONTENT' => [
                'en'    => 'Use the token below to change your password.',
                'pt_BR' => 'Use o token abaixo para alterar a sua senha.',
            ],
            'USER.PASSWORD-RESET.DISCLAIMER' => [
                'en'    => 'The token expires after one hour. If you have not requested this email, you do not need to do anything else, your password will remain the same.',
                'pt_BR' => 'O token expira após uma hora. Se você não solicitou este email, não há nada que precise ser feito. Sua senha continua a mesma.',
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
