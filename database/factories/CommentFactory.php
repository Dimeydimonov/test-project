<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->realText(200),
            'user_id' => User::factory(),
            'artwork_id' => Artwork::factory(),
            'parent_id' => null,
            'is_approved' => $this->faker->boolean(90), // 90% комментариев одобрены
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fn (array $attributes) => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
        ];
    }

    /**
     * Указать, что комментарий является ответом.
     */
    public function reply(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Comment::factory(),
        ]);
    }

    /**
     * Указать, что комментарий не одобрен.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    /**
     * Указать пользователя для комментария.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Указать произведение для комментария.
     */
    public function forArtwork(Artwork $artwork): static
    {
        return $this->state(fn (array $attributes) => [
            'artwork_id' => $artwork->id,
        ]);
    }
}
