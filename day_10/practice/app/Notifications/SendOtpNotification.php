<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otp,
        public string $type = 'verify_email'
    ) {}

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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->type === 'reset_password'
            ? 'Password Reset Code'
            : 'Email Verification Code';

        $actionText = $this->type === 'reset_password'
            ? 'Use this OTP code to reset your account password:'
            : 'Use this OTP code to verify your email address:';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello!')
            ->line($actionText)
            ->line("**{$this->otp}**")
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this action, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'type' => $this->type,
        ];
    }
}
