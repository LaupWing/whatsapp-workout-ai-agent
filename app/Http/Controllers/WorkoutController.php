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
        $workout = $this->workoutService->logWorkout($user, $validated['exercises']);

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
     * Edit Latest Workout Exercise
     */
    public function editLatestExercise(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'exercise_name' => 'required|string',
            'sets' => 'nullable|integer',
            'reps' => 'nullable|integer',
            'weight_kg' => 'nullable|numeric',
        ]);
        $user = User::find($validated['user_id']);
        $updatedExercise = $this->workoutService->editLatestExercise(
            $user,
            $validated['exercise_name'],
            $validated['sets'] ?? null,
            $validated['reps'] ?? null,
            $validated['weight_kg'] ?? null
        );

        return response()->json([
            'success' => true,
            'exercise' => $updatedExercise,
            'message' => '✅ Latest exercise updated successfully!',
        ]);
    }
}
