<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
            'content' => 'required|string|min:3|max:1000',
            'artwork_id' => [
                'required',
                'integer',
                Rule::exists('artworks', 'id')
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')
            ]
        ];
    }

    
    public function messages()
    {
        return [
            'content.required' => 'Текст комментария обязателен',
            'content.min' => 'Комментарий должен содержать минимум :min символов',
            'content.max' => 'Комментарий не должен превышать :max символов',
            'artwork_id.required' => 'ID произведения обязательно',
            'artwork_id.exists' => 'Указанное произведение не найдено',
            'parent_id.exists' => 'Указанный родительский комментарий не найден'
        ];
    }
}
