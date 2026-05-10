<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'visited_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the visitor is from today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'visited_at' => fake()->dateTimeBetween('today', 'now'),
        ]);
    }
}
