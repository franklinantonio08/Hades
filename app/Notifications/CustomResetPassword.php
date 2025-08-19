<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification // implements ShouldQueue
{
    use Queueable;

    protected string $resetUrl;
    protected int $expireMinutes;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $resetUrl)
    {
        $this->resetUrl = $resetUrl;

        // Minutos de expiración desde configuración de passwords
        $this->expireMinutes = (int) config(
            'auth.passwords.' . config('auth.defaults.passwords') . '.expire',
            60
        );
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->subject('Restablece tu contraseña - ' . Config::get('app.name'))
            ->view('mails.reset-password', [
                'user'     => $notifiable,
                'resetUrl' => $this->resetUrl,
                'expire'   => $this->expireMinutes,
                'appName'  => Config::get('app.name'),
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
