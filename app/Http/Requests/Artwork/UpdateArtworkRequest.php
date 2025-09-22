<?php

namespace App\Http\Requests\Artwork;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateArtworkRequest extends FormRequest
{
    /**
     * @var int|null
     */
    protected $artworkId;

    protected function prepareForValidation()
    {
        $this->artworkId = $this->route('artwork') ?? $this->route('id');
        
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->title)
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
            'title' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('artworks', 'slug')->ignore($this->artworkId)
            ],
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'size' => 'nullable|string|max:100',
            'materials' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240' // 10MB
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Название произведения обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
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
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Поддерживаются только изображения в формате jpeg, png, jpg, gif или webp',
            'image.max' => 'Размер изображения не должен превышать 10MB'
        ];
    }
}
