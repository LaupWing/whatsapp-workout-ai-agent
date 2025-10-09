<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test User 1: Beginner trying to lose weight (home gym)
        User::create([
            'whatsapp_number' => '31612345001',
            'name' => 'John Beginner',
            'email' => 'john.beginner@example.com',
            'gender' => 'male',
            'age' => 28,
            'height_cm' => 175.0,
            'current_weight_kg' => 85.0,
            'target_weight_kg' => 75.0,
            'fitness_goal' => 'lose_weight',
            'experience_level' => 'beginner',
            'training_location' => 'home',
            'has_dumbbells' => true,
            'workout_days' => ['Monday', 'Wednesday', 'Friday'],
            'preferred_reminder_time' => '08:00:00',
            'receive_motivation' => true,
            'whatsapp_consent' => true,
            'data_consent' => true,
            'streak_days' => 0,
            'is_active' => true,
            'onboarded_at' => now(),
        ]);

        // Test User 2: Intermediate building muscle (gym member)
        User::create([
            'whatsapp_number' => '31612345002',
            'name' => 'Sarah Lifter',
            'email' => 'sarah.lifter@example.com',
            'gender' => 'female',
            'age' => 32,
            'height_cm' => 165.0,
            'current_weight_kg' => 62.0,
            'target_weight_kg' => 65.0,
            'fitness_goal' => 'build_muscle',
            'experience_level' => 'intermediate',
            'training_location' => 'gym',
            'has_dumbbells' => false,
            'workout_days' => ['Monday', 'Tuesday', 'Thursday', 'Saturday'],
            'preferred_reminder_time' => '18:00:00',
            'receive_motivation' => true,
            'whatsapp_consent' => true,
            'data_consent' => true,
            'streak_days' => 12,
            'last_workout_date' => today()->subDay(),
            'is_active' => true,
            'onboarded_at' => now()->subWeeks(2),
        ]);

        // Test User 3: Advanced strength training (both locations)
        User::create([
            'whatsapp_number' => '31612345003',
            'name' => 'Mike Strong',
            'email' => 'mike.strong@example.com',
            'gender' => 'male',
            'age' => 35,
            'height_cm' => 182.0,
            'current_weight_kg' => 90.0,
            'target_weight_kg' => 90.0,
            'fitness_goal' => 'strength',
            'experience_level' => 'advanced',
            'training_location' => 'both',
            'has_dumbbells' => true,
            'workout_days' => ['Monday', 'Wednesday', 'Friday', 'Saturday'],
            'preferred_reminder_time' => '14:00:00',
            'receive_motivation' => true,
            'whatsapp_consent' => true,
            'data_consent' => true,
            'streak_days' => 45,
            'last_workout_date' => today(),
            'is_active' => true,
            'onboarded_at' => now()->subMonths(3),
        ]);

        // Test User 4: Inactive user (for testing reactivation)
        User::create([
            'whatsapp_number' => '31612345004',
            'name' => 'Lazy Larry',
            'email' => 'lazy.larry@example.com',
            'gender' => 'male',
            'age' => 40,
            'height_cm' => 178.0,
            'current_weight_kg' => 95.0,
            'target_weight_kg' => 80.0,
            'fitness_goal' => 'lose_weight',
            'experience_level' => 'beginner',
            'training_location' => 'gym',
            'has_dumbbells' => false,
            'workout_days' => null,
            'preferred_reminder_time' => null,
            'receive_motivation' => false,
            'whatsapp_consent' => true,
            'data_consent' => true,
            'streak_days' => 0,
            'last_workout_date' => today()->subWeeks(4),
            'is_active' => false,
            'onboarded_at' => now()->subMonths(2),
        ]);

        $this->command->info('✅ Created 4 test users');
    }
}
