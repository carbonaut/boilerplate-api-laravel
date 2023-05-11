<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User   $user
     * @param string $token
     *
     * @return void
     */
    public function __construct(
        private User $user,
        private string $token
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = __('email.USER.PASSWORD-RESET.SUBJECT');

        assert(is_string($subject));

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.password-reset',
            with: [
                'user'  => $this->user,
                'token' => $this->token,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
