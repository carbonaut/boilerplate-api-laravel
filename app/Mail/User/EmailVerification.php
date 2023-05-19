<?php

namespace App\Mail\User;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(private User $user)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        if (!$this->user->email_verification_code) {
            throw new Exception("User {$this->user->id} does not have an email verification code.");
        }

        $subject = __('email.USER.EMAIL-VERIFICATION.SUBJECT', [
            'code' => $this->user->email_verification_code,
        ]);

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
        if ($this->user->email_verified) {
            throw new Exception("User {$this->user->id} already verified the email address.");
        }

        return new Content(
            markdown: 'emails.user.email-verification',
            with: [
                'user' => $this->user,
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
