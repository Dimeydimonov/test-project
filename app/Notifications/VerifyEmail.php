<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    
    public static $createUrlCallback;

    
    public static $toMailCallback;

    public function __construct()
    {
        
    }

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return $this->buildMailMessage($verificationUrl);
    }

    
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Подтверждение адреса электронной почты')
            ->line('Пожалуйста, нажмите кнопку ниже, чтобы подтвердить ваш адрес электронной почты.')
            ->action('Подтвердить Email', $url)
            ->line('Если вы не создавали учетную запись, никаких дальнейших действий не требуется.');
    }

    
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    
    public function toArray(object $notifiable): array
    {
        return [
            
        ];
    }
}
