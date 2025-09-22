<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Токен сброса пароля обязателен',
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Пароль должен содержать не менее :min символов',
            'password.confirmed' => 'Пароли не совпадают',
            'password.regex' => 'Пароль должен содержать как минимум одну заглавную букву, одну строчную букву, одну цифру и один специальный символ',
        ];
    }
}
