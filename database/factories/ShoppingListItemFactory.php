<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingListItem>
 */
class ShoppingListItemFactory extends Factory
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
            'name' => fake()->words(2, true),
            'quantity' => fake()->randomFloat(2, 0.5, 10),
            'categories' => fake()->optional()->randomElements(['Dairy', 'Beverages', 'Produce', 'Meat', 'Bakery'], fake()->numberBetween(0, 2)),
            'in_basket' => false,
            'synced_at' => null,
        ];
    }
}
