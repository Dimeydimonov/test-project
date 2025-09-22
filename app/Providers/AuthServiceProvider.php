<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use App\Services\Implementations\Auth\AuthService;

class AuthServiceProvider extends ServiceProvider
{
    
    protected $policies = [
        
        'App\Models\Like' => 'App\Policies\LikePolicy',
        'App\Models\Artwork' => 'App\Policies\ArtworkPolicy',
    ];

    
    protected $observers = [
        
    ];
    
    
    public function register(): void
    {
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
    }

    
    public function boot()
    {
        $this->registerPolicies();
        
        
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable) {
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
            );

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Подтверждение адреса электронной почты')
                ->line('Пожалуйста, нажмите кнопку ниже, чтобы подтвердить ваш адрес электронной почты.')
                ->action('Подтвердить Email', $verifyUrl)
                ->line('Если вы не создавали учетную запись, никаких дальнейших действий не требуется.');
        });
    }
}
