<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exercise;
use App\Services\WorkoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_log_workout()
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create(['name' => 'Bench Press']);

        $workoutService = app(WorkoutService::class);

        $workout = $workoutService->logWorkout($user, [
            'exercises' => [
                [
                    'name' => 'Bench Press',
                    'sets' => 3,
                    'reps' => 10,
                    'weight_kg' => 80,
                ]
            ]
        ]);

        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'total_sets' => 3,
        ]);

        $this->assertDatabaseHas('workout_exercises', [
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'weight_kg' => 80,
        ]);
    }

    public function test_streak_increments_on_consecutive_days()
    {
        $user = User::factory()->create([
            'streak_days' => 5,
            'last_workout_date' => today()->subDay(),
        ]);

        $workoutService = app(WorkoutService::class);

        $workoutService->logWorkout($user, [
            'exercises' => [
                ['name' => 'Squat', 'sets' => 1, 'reps' => 10, 'weight_kg' => 100]
            ]
        ]);

        $user->refresh();

        $this->assertEquals(6, $user->streak_days);
        $this->assertTrue($user->last_workout_date->isToday());
    }

    public function test_streak_resets_after_gap()
    {
        $user = User::factory()->create([
            'streak_days' => 10,
            'last_workout_date' => today()->subDays(3),
        ]);

        $workoutService = app(WorkoutService::class);

        $workoutService->logWorkout($user, [
            'exercises' => [
                ['name' => 'Deadlift', 'sets' => 1, 'reps' => 5, 'weight_kg' => 140]
            ]
        ]);

        $user->refresh();

        $this->assertEquals(1, $user->streak_days);
    }
}
