<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoItem>
 */
class TodoItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'tags' => fake()->optional()->randomElements(['urgent', 'shopping', 'work', 'personal', 'home'], fake()->numberBetween(0, 3)),
            'due_date' => fake()->optional()->date(),
            'synced_at' => null,
        ];
    }
}
