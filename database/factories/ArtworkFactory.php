<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Artwork>
 */
class ArtworkFactory extends Factory
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
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'image' => $image = 'artworks/sample-' . fake()->slug() . '.jpg',
            'images' => [$image],
            'type' => fake()->randomElement(['komisi', 'personal', 'organisasi', 'fanart']),
            'form' => fake()->randomElement(['chibi', 'headshot', 'halfbody', 'fullbody']),
            'is_published' => fake()->boolean(70),
            'sort_order' => 0,
            'published_at' => fake()->boolean(70) ? now()->subDays(fake()->numberBetween(1, 30)) : null,
        ];
    }

    /**
     * Indicate that the artwork is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the artwork is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
