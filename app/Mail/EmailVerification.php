<?php

namespace App\Mail;

use App\Models\Phrase;
use App\Models\User;

class EmailVerification extends Mailable {
    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $this->user->setLocale();

        return $this
            ->subject(Phrase::getPhrase('EMAIL_EMAIL_VERIFICATION_SUBJECT', 'email', [
                '{code}' => $this->user->email_verification_code,
            ]))
            ->markdown('emails.email-verification');
    }
}
