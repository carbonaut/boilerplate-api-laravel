<?php

namespace Database\Seeders;

use App\Models\Phrase;
use Illuminate\Database\Seeder;

class PhraseApiSeeder extends Seeder
{
    private $type = 'api';

    private $phrases = [
        'ERROR_EMAIL_VERIFIED_OR_NOT_FOUND' => [
            'en'    => "This email doesn't exist or was already verified",
        ],
        'ERROR_EMAIL_OR_VERIFICATION_CODE_NOT_FOUND' => [
            'en'    => "This email doesn't exist or the verification code was not found",
        ],
        'ERROR_EMAIL_VERIFICATION_CODE_EXPIRED' => [
            'en'    => 'The verification code has expired. Please request a new one.',
        ],
        'ERROR_PUSH_FROM_ANOTHER_USER' => [
            'en'    => 'This push is for another person',
        ],
        'ERROR_EMAIL_NOT_VERIFIED' => [
            'en'    => 'The email address is not verified',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        Phrase::where('type', $this->type)->delete();

        foreach ($this->phrases as $phrase => $translations) {
            $p = new Phrase();
            $p->key = $phrase;
            $p->type = $this->type;

            foreach ($translations as $locale => $value) {
                $p->translateOrNew($locale)->value = $value;
            }
            $p->save();
        }
    }
}
