<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\Exercise;
use Carbon\Carbon;

class WorkoutService
{
    /**
     * Log a workout from structured data
     * Called by ADK agent after parsing user input
     */
    public function logWorkout(User $user, array $workoutData): Workout
    {
        // Get or create today's workout
        $workout = Workout::firstOrCreate(
            [
                'user_id' => $user->id,
                'workout_date' => $workoutData['date'] ?? today(),
            ],
            [
                'workout_type' => $workoutData['type'] ?? null,
                'start_time' => $workoutData['start_time'] ?? now()->format('H:i:s'),
            ]
        );

        // Add exercise sets
        $this->addExerciseToWorkout($workout, $workoutData);

        // Update workout totals
        $this->updateWorkoutTotals($workout);

        // Update user streak
        $this->updateUserStreak($user);

        return $workout->fresh();
    }

    /**
     * Add exercise to workout
     */
    private function addExerciseToWorkout(Workout $workout, array $exerciseData): void
    {
        // Find exercise by name or aliases
        $exercise = Exercise::where('name', $exerciseData['exercise_name'])
            ->orWhereJsonContains('aliases', strtolower($exerciseData['exercise_name']))
            ->first();

        if (!$exercise) {
            // Create new exercise if not found
            $exercise = Exercise::create([
                'name' => $exerciseData['exercise_name'],
                'category' => 'strength',
                'muscle_group' => $exerciseData['muscle_group'] ?? 'unknown',
            ]);
        }

        // Get next set number for this exercise in this workout
        $setNumber = WorkoutExercise::where('workout_id', $workout->id)
            ->where('exercise_id', $exercise->id)
            ->max('set_number') + 1;

        // Add sets
        $sets = $exerciseData['sets'] ?? 1;
        for ($i = 0; $i < $sets; $i++) {
            WorkoutExercise::create([
                'workout_id' => $workout->id,
                'exercise_id' => $exercise->id,
                'set_number' => $setNumber + $i,
                'reps' => $exerciseData['reps'] ?? null,
                'weight_kg' => $exerciseData['weight_kg'] ?? null,
                'duration_seconds' => $exerciseData['duration_seconds'] ?? null,
                'distance_km' => $exerciseData['distance_km'] ?? null,
                'rpe' => $exerciseData['rpe'] ?? null,
                'notes' => $exerciseData['notes'] ?? null,
            ]);
        }

        // Check if this is a PR
        $this->checkForPR($workout->user_id, $exercise->id, $exerciseData);
    }

    /**
     * Update workout totals
     */
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
            'end_time' => now()->format('H:i:s'),
        ]);

        // Calculate duration if start_time exists
        if ($workout->start_time) {
            $start = Carbon::parse($workout->start_time);
            $end = Carbon::parse($workout->end_time);
            $workout->update([
                'duration_minutes' => $end->diffInMinutes($start),
            ]);
        }
    }

    /**
     * Check if this is a personal record
     */
    private function checkForPR(int $userId, int $exerciseId, array $exerciseData): void
    {
        if (!isset($exerciseData['weight_kg']) || !isset($exerciseData['reps'])) {
            return;
        }

        // Get user's best for this exercise
        $bestPrevious = WorkoutExercise::whereHas('workout', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->where('exercise_id', $exerciseId)
            ->where('weight_kg', '>=', $exerciseData['weight_kg'])
            ->where('reps', '>=', $exerciseData['reps'])
            ->exists();

        // If no previous record beats this, it's a PR
        if (!$bestPrevious) {
            WorkoutExercise::whereHas('workout', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
                ->where('exercise_id', $exerciseId)
                ->latest()
                ->first()
                ?->update(['is_pr' => true]);
        }
    }

    /**
     * Update user workout streak
     */
    private function updateUserStreak(User $user): void
    {
        $lastWorkout = $user->last_workout_date;
        $today = today();

        if (!$lastWorkout) {
            // First workout ever
            $user->update([
                'streak_days' => 1,
                'last_workout_date' => $today,
            ]);
            return;
        }

        $daysSinceLastWorkout = $lastWorkout->diffInDays($today);

        if ($daysSinceLastWorkout === 0) {
            // Already worked out today
            return;
        } elseif ($daysSinceLastWorkout === 1) {
            // Consecutive day - increase streak
            $user->increment('streak_days');
        } else {
            // Streak broken - reset
            $user->update(['streak_days' => 1]);
        }

        $user->update(['last_workout_date' => $today]);
    }

    /**
     * Get workout history summary
     */
    public function getWorkoutSummary(User $user, int $days = 7): array
    {
        $workouts = Workout::where('user_id', $user->id)
            ->where('workout_date', '>=', today()->subDays($days))
            ->with('workoutExercises.exercise')
            ->get();

        return [
            'total_workouts' => $workouts->count(),
            'total_volume_kg' => $workouts->sum('total_volume_kg'),
            'total_sets' => $workouts->sum('total_sets'),
            'average_duration' => round($workouts->avg('duration_minutes')),
            'workout_days' => $workouts->pluck('workout_date')->map(fn($d) => $d->format('Y-m-d'))->toArray(),
            'exercises_performed' => $workouts->flatMap(fn($w) => $w->workoutExercises)
                ->pluck('exercise.name')
                ->unique()
                ->values()
                ->toArray(),
        ];
    }

    /**
     * Edit latest workout exercise entry
     */
    public function editLatestExercise(User $user, array $editData): ?WorkoutExercise
    {
        $latestEntry = WorkoutExercise::whereHas('workout', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->first();
        if (!$latestEntry) {
            return null;
        }
        $latestEntry->update($editData);
        return $latestEntry->fresh();
    }
}
