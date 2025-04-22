<?php

namespace App\Notifications\User;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Determine if the notification should be sent.
     *
     * @param User   $notifiable
     * @param string $channel
     *
     * @return bool
     */
    public function shouldSend(User $notifiable, string $channel): bool
    {
        return $notifiable->email_verification_code && !$notifiable->email_verified;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param User $notifiable
     *
     * @return MailMessage
     */
    public function toMail(User $notifiable): MailMessage
    {
        if (!$notifiable->email_verification_code) {
            throw new Exception("User {$notifiable->id} does not have an email verification code.");
        }

        return (new MailMessage())
            ->subject(
                __('notifications.USER.EMAIL-VERIFICATION.SUBJECT', [
                    'code' => $notifiable->email_verification_code,
                ])
            )
            ->markdown('notifications.emails.user.email-verification', [
                'user' => $notifiable,
            ])
        ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
