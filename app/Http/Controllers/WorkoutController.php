<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'workout_data' => 'required|array',
            'workout_data.date' => 'nullable|date',
            'workout_data.type' => 'nullable|string',
            'workout_data.exercises' => 'required|array',
            'workout_data.exercises.*.name' => 'required|string',
            'workout_data.exercises.*.sets' => 'nullable|integer',
            'workout_data.exercises.*.reps' => 'nullable|integer',
            'workout_data.exercises.*.weight_kg' => 'nullable|numeric',
        ]);

        $user = User::find($validated['user_id']);
        $workout = $this->workoutService->logWorkout($user, $validated['workout_data']);

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
}
