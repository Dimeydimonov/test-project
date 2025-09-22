<?php

namespace App\Services\Interfaces\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface AuthServiceInterface
{
    
    public function register(array $data): User;

    
    public function login(array $credentials): array;

    
    public function sendEmailVerificationNotification(User $user): void;

    
    public function logout(): void;

    
    public function getCurrentUser();

    
    public function updateUser(array $data): User;

    
    public function updatePassword(string $currentPassword, string $newPassword): bool;

    
    public function sendPasswordResetLink(string $email): string;

    
    public function resetPassword(array $data): string;
}
