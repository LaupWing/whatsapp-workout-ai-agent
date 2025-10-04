<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'category' => 'strength',
            'muscle_group' => $this->faker->randomElement(['chest', 'back', 'legs', 'shoulders', 'arms']),
            'equipment' => $this->faker->randomElement(['barbell', 'dumbbell', 'machine', 'bodyweight']),
            'difficulty' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'is_active' => true,
        ];
    }
}
