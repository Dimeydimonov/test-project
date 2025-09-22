<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Авторизация проверяется в контроллере
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password',
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
            'current_password.required' => 'Текущий пароль обязателен для заполнения',
            'current_password.current_password' => 'Неверный текущий пароль',
            'new_password.required' => 'Новый пароль обязателен для заполнения',
            'new_password.min' => 'Пароль должен содержать не менее :min символов',
            'new_password.confirmed' => 'Пароли не совпадают',
            'new_password.different' => 'Новый пароль должен отличаться от текущего',
            'new_password.regex' => 'Пароль должен содержать как минимум одну заглавную букву, одну строчную букву, одну цифру и один специальный символ',
        ];
    }
}
