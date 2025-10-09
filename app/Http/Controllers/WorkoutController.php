<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WorkoutService;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function __construct(
        private WorkoutService $workoutService
    ) {}

    /**
     * Log workout (called by ADK agent)
     */
    public function log(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_name' => 'required|string',
            'exercises.*.sets' => 'required|integer',
            'exercises.*.reps' => 'required|integer',
            'exercises.*.weight_kg' => 'required|numeric',
        ]);

        $user = User::find($validated['user_id']);
        foreach ($validated['exercises'] as $exercise) {
            $exercise['date'] = $request->input('date', today()->toDateString());
            $exercise['type'] = $request->input('type', null);
            $exercise['start_time'] = $request->input('start_time', now()->format('H:i:s'));
            $workout = $this->workoutService->logWorkout($user, $exercise);
        }

        return response()->json([
            'success' => true,
            'workout' => $workout->load('workoutExercises.exercise'),
            'message' => '💪 Workout logged successfully!',
        ]);
    }

    /**
     * Get workout history
     */
    public function history(Request $request)
    {
        $user = User::find($request->user_id);
        $days = $request->input('days', 30);

        $workouts = $user->workouts()
            ->where('workout_date', '>=', today()->subDays($days))
            ->with('workoutExercises.exercise')
            ->orderBy('workout_date', 'desc')
            ->get();

        return response()->json([
            'workouts' => $workouts,
        ]);
    }

    /**
     * Get workout summary
     */
    public function summary(Request $request)
    {
        $user = User::find($request->user_id);
        $days = $request->input('days', 7);

        $summary = $this->workoutService->getWorkoutSummary($user, $days);

        return response()->json($summary);
    }

    /**
     * Delete a workout
     */
    public function delete(Request $request, $id)
    {
        $user = User::find($request->user_id);
        $workout = $user->workouts()->find($id);

        if (!$workout) {
            return response()->json([
                'success' => false,
                'message' => 'Workout not found',
            ], 404);
        }

        $workout->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workout deleted successfully',
        ]);
    }

    /**
     * Edit workout exercises
     */
    public function editExercises(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'exercises' => 'required|array|min:1',
            'exercises.*.workout_exercise_id' => 'required|exists:workout_exercises,id',
            'exercises.*.weight_kg' => 'nullable|numeric',
            'exercises.*.reps' => 'nullable|integer',
            'exercises.*.duration_seconds' => 'nullable|integer',
            'exercises.*.distance_km' => 'nullable|numeric',
            'exercises.*.rpe' => 'nullable|integer|min:1|max:10',
            'exercises.*.notes' => 'nullable|string',
        ]);

        $user = User::find($validated['user_id']);
        $updatedExercises = [];

        foreach ($validated['exercises'] as $exerciseData) {
            // Verify the workout exercise belongs to the user
            $workoutExercise = \App\Models\WorkoutExercise::whereHas('workout', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->find($exerciseData['workout_exercise_id']);

            if (!$workoutExercise) {
                return response()->json([
                    'success' => false,
                    'message' => "Workout exercise {$exerciseData['workout_exercise_id']} not found or doesn't belong to user",
                ], 404);
            }

            // Prepare update data (only include fields that were provided)
            $updateData = collect($exerciseData)
                ->except('workout_exercise_id')
                ->filter(fn($value) => !is_null($value))
                ->toArray();

            $workoutExercise->update($updateData);
            $updatedExercises[] = $workoutExercise->fresh('exercise');
        }

        // Update workout totals if exercises were modified
        if (!empty($updatedExercises)) {
            $workout = $updatedExercises[0]->workout;

            // Recalculate workout totals
            $exercises = $workout->workoutExercises;
            $totalVolume = $exercises->sum(fn($we) => ($we->reps ?? 0) * ($we->weight_kg ?? 0));
            $totalSets = $exercises->count();

            $workout->update([
                'total_volume_kg' => $totalVolume,
                'total_sets' => $totalSets,
            ]);
        }

        return response()->json([
            'success' => true,
            'exercises' => $updatedExercises,
            'message' => '✅ Exercises updated successfully!',
        ]);
    }
}
