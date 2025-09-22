<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use App\Services\Implementations\Auth\AuthService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Like' => 'App\Policies\LikePolicy',
        'App\Models\Artwork' => 'App\Policies\ArtworkPolicy',
    ];

    /**
     * @var array
     */
    protected $observers = [
        // User::class => [UserObserver::class],
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
    }

    /**
     * Bootstrap any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // Email Verification
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
