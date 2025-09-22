<?php

namespace App\Services\Interfaces\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface AuthServiceInterface
{
    /**
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data): User;

    /**
     * @param array $credentials
     * @return array
     * @throws \Exception If authentication fails or email is not verified
     */
    public function login(array $credentials): array;

    /**
     * @param User $user
     * @return void
     */
    public function sendEmailVerificationNotification(User $user): void;

    /**
     * @return void
     */
    public function logout(): void;

    /**
     * @return User|Model|null
     */
    public function getCurrentUser();

    /**
     * @param array $data
     * @return User
     */
    public function updateUser(array $data): User;

    /**
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(string $currentPassword, string $newPassword): bool;

    /**
     * @param string $email
     * @return string
     */
    public function sendPasswordResetLink(string $email): string;

    /**
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string;
}
