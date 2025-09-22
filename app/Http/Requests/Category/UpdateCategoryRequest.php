<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * @var int|null
     */
    protected $categoryId;

    protected function prepareForValidation()
    {
        $this->categoryId = $this->route('category') ?? $this->route('id');
        
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name)
            ]);
        }
    }


    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->categoryId)
            ],
            'description' => 'nullable|string',
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120' // 5MB
            ],
            'order' => 'sometimes|nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.max' => 'Название категории не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
            'slug.unique' => 'Категория с таким URL-адресом уже существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Поддерживаются только изображения в формате jpeg, png, jpg, gif или webp',
            'image.max' => 'Размер изображения не должен превышать 5MB',
            'order.integer' => 'Порядок должен быть целым числом',
            'order.min' => 'Порядок не может быть отрицательным',
            'is_active.boolean' => 'Некорректное значение для статуса активности',
            'meta_title.max' => 'Мета-заголовок не должен превышать 255 символов',
            'meta_description.max' => 'Мета-описание не должно превышать 500 символов',
            'meta_keywords.max' => 'Ключевые слова не должны превышать 255 символов',
        ];
    }
}
