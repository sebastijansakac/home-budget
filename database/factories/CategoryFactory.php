<?php

namespace Database\Factories;

use App\Enum\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(),
            'type' => fake()->randomElement([ExpenseType::Income->name, ExpenseType::Outcome->name]),
        ];
    }
}
