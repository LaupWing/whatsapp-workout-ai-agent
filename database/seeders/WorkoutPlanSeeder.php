<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class WorkoutPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Get Sarah (user #2)
        $user = User::find(2);

        // Create PPL (Push/Pull/Legs) plan
        $plan = WorkoutPlan::create([
            'user_id' => $user->id,
            'name' => 'Push/Pull/Legs Split',
            'description' => '6-day intermediate hypertrophy program',
            'goal' => 'hypertrophy',
            'duration_weeks' => 12,
            'start_date' => today()->subWeeks(2),
            'status' => 'active',
            'schedule' => [
                'monday' => 'push',
                'tuesday' => 'pull',
                'wednesday' => 'legs',
                'thursday' => 'push',
                'friday' => 'pull',
                'saturday' => 'legs',
                'sunday' => 'rest',
            ],
        ]);

        // Get exercises
        $bench = Exercise::where('name', 'Bench Press')->first();
        $ohp = Exercise::where('name', 'Overhead Press')->first();
        $deadlift = Exercise::where('name', 'Deadlift')->first();
        $pullups = Exercise::where('name', 'Pull-ups')->first();
        $squat = Exercise::where('name', 'Squat')->first();
        $rdl = Exercise::where('name', 'Romanian Deadlift')->first();

        // Monday - Push
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $bench->id,
                'day_of_week' => 'monday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '8-10',
                'target_weight_kg' => 60,
                'rest_seconds' => 120,
            ],
            [
                'exercise_id' => $ohp->id,
                'day_of_week' => 'monday',
                'order' => 2,
                'target_sets' => 3,
                'target_reps' => '8-10',
                'target_weight_kg' => 35,
                'rest_seconds' => 90,
            ],
        ]);

        // Tuesday - Pull
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $deadlift->id,
                'day_of_week' => 'tuesday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '5',
                'target_weight_kg' => 100,
                'rest_seconds' => 180,
            ],
            [
                'exercise_id' => $pullups->id,
                'day_of_week' => 'tuesday',
                'order' => 2,
                'target_sets' => 4,
                'target_reps' => 'AMRAP',
                'rest_seconds' => 120,
            ],
        ]);

        // Wednesday - Legs
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $squat->id,
                'day_of_week' => 'wednesday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '6-8',
                'target_weight_kg' => 90,
                'rest_seconds' => 180,
            ],
            [
                'exercise_id' => $rdl->id,
                'day_of_week' => 'wednesday',
                'order' => 2,
                'target_sets' => 3,
                'target_reps' => '10-12',
                'target_weight_kg' => 70,
                'rest_seconds' => 90,
            ],
        ]);

        // Thursday - Push (same as Monday)
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $bench->id,
                'day_of_week' => 'thursday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '8-10',
                'target_weight_kg' => 60,
                'rest_seconds' => 120,
            ],
            [
                'exercise_id' => $ohp->id,
                'day_of_week' => 'thursday',
                'order' => 2,
                'target_sets' => 3,
                'target_reps' => '8-10',
                'target_weight_kg' => 35,
                'rest_seconds' => 90,
            ],
        ]);

        // Friday - Pull (same as Tuesday)
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $deadlift->id,
                'day_of_week' => 'friday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '5',
                'target_weight_kg' => 100,
                'rest_seconds' => 180,
            ],
            [
                'exercise_id' => $pullups->id,
                'day_of_week' => 'friday',
                'order' => 2,
                'target_sets' => 4,
                'target_reps' => 'AMRAP',
                'rest_seconds' => 120,
            ],
        ]);

        // Saturday - Legs (same as Wednesday)
        $plan->planExercises()->createMany([
            [
                'exercise_id' => $squat->id,
                'day_of_week' => 'saturday',
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => '6-8',
                'target_weight_kg' => 90,
                'rest_seconds' => 180,
            ],
            [
                'exercise_id' => $rdl->id,
                'day_of_week' => 'saturday',
                'order' => 2,
                'target_sets' => 3,
                'target_reps' => '10-12',
                'target_weight_kg' => 70,
                'rest_seconds' => 90,
            ],
        ]);

        // Sunday - Rest (no exercises)

        $this->command->info('✅ Created PPL workout plan for Sarah with all 7 days');
    }
}
