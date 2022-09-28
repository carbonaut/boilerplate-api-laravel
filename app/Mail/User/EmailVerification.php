<?php

namespace App\Mail\User;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    private $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->user->email_verified) {
            throw new Exception("User {$this->user->id} already verified the email address.");
        }

        if (!$this->user->email_verification_code) {
            throw new Exception("User {$this->user->id} does not have a verification code.");
        }

        return $this
            ->subject(strval(__('email.USER.EMAIL-VERIFICATION.SUBJECT', [
                'code' => $this->user->email_verification_code,
            ])))
            ->markdown('emails.user.email-verification', [
                'user' => $this->user,
            ]);
    }
}
