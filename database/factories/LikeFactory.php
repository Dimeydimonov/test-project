<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'likeable_id' => Artwork::factory(),
            'likeable_type' => Artwork::class,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Указать пользователя, который поставил лайк.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Указать произведение, которое лайкнули.
     */
    public function forArtwork(Artwork $artwork): static
    {
        return $this->state(fn (array $attributes) => [
            'likeable_id' => $artwork->id,
            'likeable_type' => get_class($artwork),
        ]);
    }

    /**
     * Указать дату создания лайка.
     */
    public function createdAt($date): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
