<?php

namespace App\Http\Requests\Artwork;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreArtworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->title)
            ]);
        }
    }

    
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('artworks', 'slug')
            ],
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'size' => 'nullable|string|max:100',
            'materials' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240' 
            ]
        ];
    }

    
    public function messages(): array
    {
        return [
            'title.required' => 'Название произведения обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.unique' => 'Произведение с таким URL-адресом уже существует',
            'year.integer' => 'Год должен быть целым числом',
            'year.min' => 'Год должен быть не менее 1000',
            'year.max' => 'Год не может быть больше текущего',
            'size.max' => 'Размер не должен превышать 100 символов',
            'materials.max' => 'Материалы не должны превышать 255 символов',
            'price.numeric' => 'Цена должна быть числом',
            'price.min' => 'Цена не может быть отрицательной',
            'is_available.boolean' => 'Некорректное значение доступности',
            'is_featured.boolean' => 'Некорректное значение для избранного',
            'category_id.required' => 'Необходимо указать категорию',
            'category_id.exists' => 'Выбранная категория не найдена',
            'image.required' => 'Изображение обязательно для загрузки',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Поддерживаются только изображения в формате jpeg, png, jpg, gif или webp',
            'image.max' => 'Размер изображения не должен превышать 10MB'
        ];
    }
}
