<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailCustom extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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

    protected function verificationUrl($notifiable)
    {
        $expiration = Carbon::now()->addMinutes(
            Config::get('auth.verification.expire', 60)
        );

        return URL::temporarySignedRoute(
            'verification.verify',
            $expiration,
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
       $verificationUrl = $this->verificationUrl($notifiable);

        // Usamos una vista HTML propia (no markdown)
        return (new MailMessage)
            ->subject('Verifica tu correo - ' . Config::get('app.name'))
            ->view('mails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'appName' => Config::get('app.name'),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
