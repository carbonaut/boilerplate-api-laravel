<?php

namespace App\Mail;

use App\Models\Phrase;
use App\Models\User;

class PasswordReset extends Mailable {
    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    /**
     * The token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user, string $token) {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $this->user->setLocale();

        return $this
            ->subject(Phrase::getPhrase('EMAIL_PASSWORD_RESET_SUBJECT', 'email'))
            ->markdown('emails.password-reset')
            ->with([
                'url' => 'https://reset.' . config('app.domain') . '/?token=' . $this->token . ($this->user->language_id !== null ? '&lang=' . $this->user->language->locale : ''),
            ]);
    }
}
