<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        // Get test user (Sarah - the intermediate one)
        $user = User::where('whatsapp_number', '31612345002')->first();

        if (!$user) {
            $this->command->error('❌ User not found. Run UserSeeder first.');
            return;
        }

        // Get exercises
        $benchPress = Exercise::where('name', 'Bench Press')->first();
        $squat = Exercise::where('name', 'Squat')->first();
        $deadlift = Exercise::where('name', 'Deadlift')->first();
        $pullups = Exercise::where('name', 'Pull-ups')->first();
        $overheadPress = Exercise::where('name', 'Overhead Press')->first();

        // Workout 1: Push day (3 days ago)
        $workout1 = Workout::create([
            'user_id' => $user->id,
            'workout_date' => today()->subDays(3),
            'start_time' => '18:00:00',
            'end_time' => '19:15:00',
            'duration_minutes' => 75,
            'workout_type' => 'push',
            'notes' => 'Felt strong today!',
            'energy_level' => 'high',
            'rating' => 5,
        ]);

        // Bench Press - 4 sets
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $benchPress->id,
            'set_number' => 1,
            'reps' => 8,
            'weight_kg' => 60.0,
            'rpe' => 7,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $benchPress->id,
            'set_number' => 2,
            'reps' => 8,
            'weight_kg' => 60.0,
            'rpe' => 8,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $benchPress->id,
            'set_number' => 3,
            'reps' => 7,
            'weight_kg' => 60.0,
            'rpe' => 9,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $benchPress->id,
            'set_number' => 4,
            'reps' => 6,
            'weight_kg' => 60.0,
            'rpe' => 9,
            'notes' => 'Last set was tough',
        ]);

        // Overhead Press - 3 sets
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $overheadPress->id,
            'set_number' => 1,
            'reps' => 10,
            'weight_kg' => 35.0,
            'rpe' => 7,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $overheadPress->id,
            'set_number' => 2,
            'reps' => 9,
            'weight_kg' => 35.0,
            'rpe' => 8,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout1->id,
            'exercise_id' => $overheadPress->id,
            'set_number' => 3,
            'reps' => 8,
            'weight_kg' => 35.0,
            'rpe' => 8,
        ]);

        // Update workout totals
        $this->updateWorkoutTotals($workout1);

        // Workout 2: Pull day (2 days ago)
        $workout2 = Workout::create([
            'user_id' => $user->id,
            'workout_date' => today()->subDays(2),
            'start_time' => '18:30:00',
            'end_time' => '19:45:00',
            'duration_minutes' => 75,
            'workout_type' => 'pull',
            'notes' => 'Back felt great',
            'energy_level' => 'medium',
            'rating' => 4,
        ]);

        // Deadlift - 5 sets
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $deadlift->id,
            'set_number' => 1,
            'reps' => 5,
            'weight_kg' => 80.0,
            'rpe' => 6,
            'is_warmup' => true,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $deadlift->id,
            'set_number' => 2,
            'reps' => 5,
            'weight_kg' => 100.0,
            'rpe' => 7,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $deadlift->id,
            'set_number' => 3,
            'reps' => 5,
            'weight_kg' => 100.0,
            'rpe' => 8,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $deadlift->id,
            'set_number' => 4,
            'reps' => 5,
            'weight_kg' => 100.0,
            'rpe' => 9,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $deadlift->id,
            'set_number' => 5,
            'reps' => 5,
            'weight_kg' => 100.0,
            'rpe' => 9,
            'is_pr' => true,
            'notes' => 'New PR!',
        ]);

        // Pull-ups - 4 sets
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $pullups->id,
            'set_number' => 1,
            'reps' => 8,
            'rpe' => 7,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $pullups->id,
            'set_number' => 2,
            'reps' => 7,
            'rpe' => 8,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $pullups->id,
            'set_number' => 3,
            'reps' => 6,
            'rpe' => 9,
        ]);
        WorkoutExercise::create([
            'workout_id' => $workout2->id,
            'exercise_id' => $pullups->id,
            'set_number' => 4,
            'reps' => 5,
            'rpe' => 9,
        ]);

        $this->updateWorkoutTotals($workout2);

        // Workout 3: Leg day (yesterday)
        $workout3 = Workout::create([
            'user_id' => $user->id,
            'workout_date' => today()->subDay(),
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'duration_minutes' => 90,
            'workout_type' => 'legs',
            'notes' => 'Legs are destroyed',
            'energy_level' => 'medium',
            'rating' => 5,
        ]);

        // Squats - 5 sets
        for ($i = 1; $i <= 5; $i++) {
            WorkoutExercise::create([
                'workout_id' => $workout3->id,
                'exercise_id' => $squat->id,
                'set_number' => $i,
                'reps' => 5,
                'weight_kg' => 90.0,
                'rpe' => 6 + $i,
            ]);
        }

        $this->updateWorkoutTotals($workout3);

        $this->command->info('✅ Created 3 sample workouts for Sarah');
    }

    private function updateWorkoutTotals(Workout $workout): void
    {
        $exercises = WorkoutExercise::where('workout_id', $workout->id)->get();

        $totalVolume = $exercises->sum(function ($we) {
            return ($we->reps ?? 0) * ($we->weight_kg ?? 0);
        });

        $totalSets = $exercises->count();

        $workout->update([
            'total_volume_kg' => $totalVolume,
            'total_sets' => $totalSets,
        ]);
    }
}
