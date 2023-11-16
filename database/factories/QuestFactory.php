<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class QuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->numberBetween(1, 7),
            'status' => $this->faker->numberBetween(8, 11),
            'required' => $this->faker->numberBetween(1, 100),
            'collected' => $this->faker->numberBetween(1, 100),
            'reward' => $this->faker->numberBetween(1, 100),
        ];
    }
}
