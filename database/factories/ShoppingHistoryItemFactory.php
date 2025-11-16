<?php

namespace Database\Factories;

use App\Models\ShoppingHistoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingHistoryItem>
 */
class ShoppingHistoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // Should be set when creating
            'name' => fake()->words(2, true),
            'quantity' => fake()->randomFloat(2, 0.5, 10),
            'categories' => fake()->optional()->randomElements(['Dairy', 'Beverages', 'Produce', 'Meat', 'Bakery'], fake()->numberBetween(0, 2)),
            'purchased_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
