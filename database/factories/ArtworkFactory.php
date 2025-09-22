<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class ArtworkFactory extends Factory
{
    
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(3),
            'year' => $this->faker->numberBetween(2000, 2025),
            'size' => $this->faker->randomElement(['30x40 cm', '40x50 cm', '50x70 cm', '60x80 cm']),
            'materials' => $this->faker->randomElement(['Холст, масло', 'Акрил', 'Акварель', 'Графика']),
            'price' => $this->faker->numberBetween(1000, 100000),
            'image_path' => 'artworks/' . $this->faker->image('public/storage/artworks', 800, 1000, null, false),
            'image_alt' => $this->faker->sentence,
            'is_available' => $this->faker->boolean(80), 
            'is_featured' => $this->faker->boolean(20), 
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];
    }
}
