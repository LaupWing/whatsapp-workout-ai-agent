<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'whatsapp_number' => $this->faker->unique()->numerify('316########'),
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'age' => $this->faker->numberBetween(18, 65),
            'height_cm' => $this->faker->randomFloat(2, 150, 200),
            'current_weight_kg' => $this->faker->randomFloat(2, 50, 120),
            'fitness_goal' => $this->faker->randomElement(['lose_weight', 'build_muscle', 'maintain', 'strength']),
            'experience_level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'is_active' => true,
        ];
    }
}
