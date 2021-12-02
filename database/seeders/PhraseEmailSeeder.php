<?php

namespace Database\Seeders;

use App\Models\Phrase;
use Illuminate\Database\Seeder;

class PhraseEmailSeeder extends Seeder
{
    private $type = 'email';

    private $phrases = [
        // INTROS and OUTROS
        'EMAIL_INTRO_NAME_WELCOME' => [
            'en'    => '<p>Welcome <strong>{full_name}</strong>!</p>',
        ],
        'EMAIL_INTRO_NAME_NO_PROBLEM' => [
            'en'    => '<p>No problem at all <strong>{full_name}</strong>,</p>',
        ],
        'EMAIL_INTRO_NAME_HELLO' => [
            'en'    => '<p>Hello <strong>{full_name}</strong></p>',
        ],
        'EMAIL_INTRO_WELCOME' => [
            'en'    => '<p>Welcome!</p>',
        ],
        'EMAIL_OUTRO_TEAM' => [
            'en'    => '<p>Your {team} Team</p>',
        ],
        'EMAIL_OUTRO_LOOKING_FORWARD' => [
            'en'    => '<p>We are looking forward to you</p>',
        ],
        'EMAIL_OUTRO_SEE_YOU_LATER' => [
            'en'    => '<p>See you later!</p>',
        ],
        'EMAIL_OUTRO_TAKE_CARE' => [
            'en'    => '<p>Take care of yourself!</p>',
        ],

        // FOOTER
        'EMAIL_FOOTER' => [
            'en'    => '<p>Need help with anything? Call us at <a href="tel:{support_phone_raw}">{support_phone}</a>
                        <br/>
                        or email <a href="mailto:{support_email}">{support_email}</a></p>
                        <p>{team} - {address}</p>',
        ],

        // PASSWORD RESET
        'EMAIL_PASSWORD_RESET_SUBJECT' => [
            'en'    => 'Forgot your password?',
        ],
        'EMAIL_PASSWORD_RESET_TITLE' => [
            'en'    => 'Forgot your password?',
        ],
        'EMAIL_PASSWORD_RESET_CONTENT_1' => [
            'en'    => '<p>You can easily change your password via the following link:</p>',
        ],
        'EMAIL_PASSWORD_RESET_BUTTON' => [
            'en'    => 'Change your password',
        ],
        'EMAIL_PASSWORD_RESET_CONTENT_2' => [
            'en'    => '<p>Please note, the link expires after one hour. You can request a new link <a href="{reset}">here</a>.</p>
                        <p>If you have not requested this email, you do not need to do anything else, your password will remain the same.</p>                
                        <p>Alternatively, you can also change your password within one hour using the following link:</p>
                        <p><a href="{url}">{url}</a></p>',
        ],

        // EMAIL VERIFICATION
        'EMAIL_EMAIL_VERIFICATION_SUBJECT' => [
            'en'    => 'Welcome! Use {code} to confirm your email address',
        ],
        'EMAIL_EMAIL_VERIFICATION_TITLE' => [
            'en'    => 'Confirm your email',
        ],
        'EMAIL_EMAIL_VERIFICATION_CONTENT_1' => [
            'en'    => '<p>In order to use your account to its full extent, please enter the following code as confirmation in the app:</p>',
        ],
        'EMAIL_EMAIL_VERIFICATION_CONTENT_2' => [
            'en'    => '<p>Please remember the email address you used to register, as it serves as your user name: <strong>{email}</strong></p>',
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
