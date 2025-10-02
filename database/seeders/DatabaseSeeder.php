<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ExerciseSeeder::class,
            UserSeeder::class,
            WorkoutSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📱 Test Users Created:');
        $this->command->info('  1. John Beginner (31612345001) - Lose weight, Beginner');
        $this->command->info('  2. Sarah Lifter (31612345002) - Build muscle, Intermediate ⭐ HAS WORKOUTS');
        $this->command->info('  3. Mike Strong (31612345003) - Strength, Advanced');
        $this->command->info('  4. Lazy Larry (31612345004) - Inactive user');
        $this->command->info('');
    }
}
