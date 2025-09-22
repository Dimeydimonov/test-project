<?php

namespace App\Services\Implementations\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class AuthService implements AuthServiceInterface
{
    
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'email_verified_at' => null
        ]);
    }

    
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Неверный email или пароль');
        }

        $user = Auth::user();
        
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            throw new \Exception('Пожалуйста, подтвердите ваш email перед входом.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    
    public function logout(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }
    
    
    public function sendEmailVerificationNotification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    
    public function getCurrentUser()
    {
        return Auth::user();
    }

    
    public function updateUser(array $data): User
    {
        $user = Auth::user();
        $user->update($data);
        
        return $user->fresh();
    }

    
    public function updatePassword(string $currentPassword, string $newPassword): bool
    {
        $user = Auth::user();
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Текущий пароль неверный', 422);
        }
        
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    
    public function sendPasswordResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);
        
        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Не удалось отправить ссылку для сброса пароля', 500);
        }
        
        return $status;
    }

    
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );
        
        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception('Не удалось сбросить пароль', 500);
        }
        
        return $status;
    }
}
