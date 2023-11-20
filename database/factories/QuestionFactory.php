<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence,
            'answer_a' => $this->faker->sentence,
            'answer_b' => $this->faker->sentence,
            'answer_c' => $this->faker->sentence,
            'answer_d' => $this->faker->sentence,
            'correct' => $this->faker->numberBetween(1, 4),
            'prize' => $this->faker->numberBetween(10, 100),
        ];
    }
}
