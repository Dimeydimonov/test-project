<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'likeable_id' => Artwork::factory(),
            'likeable_type' => Artwork::class,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    
    public function forArtwork(Artwork $artwork): static
    {
        return $this->state(fn (array $attributes) => [
            'likeable_id' => $artwork->id,
            'likeable_type' => get_class($artwork),
        ]);
    }

    
    public function createdAt($date): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
