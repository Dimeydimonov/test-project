<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class CategoryFactory extends Factory
{
    
    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'image_path' => 'categories/' . $this->faker->image('public/storage/categories', 640, 480, null, false),
            'order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(90), 
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->paragraph,
            'meta_keywords' => implode(', ', $this->faker->words(5)),
        ];
    }
}
