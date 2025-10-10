<?php

namespace App\Http\Controllers;

use App\Enums\WorkoutPlanStatus;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutPlanController extends Controller
{
    /**
     * Store a new workout plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal' => 'required|string',
            'muscle_groups' => 'required|array',
            'muscle_groups.*' => 'string',
            'primary_focus' => 'nullable|string',
            'session_duration' => 'required|integer|min:15|max:180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Create workout plan
        $workoutPlan = WorkoutPlan::create([
            'user_id' => $user->id,
            'name' => 'Custom Workout Plan',
            'description' => 'AI-generated workout plan',
            'goal' => $request->goal,
            'status' => WorkoutPlanStatus::ACTIVE,
            'schedule' => [
                'muscle_groups' => $request->muscle_groups,
                'primary_focus' => $request->primary_focus,
                'session_duration' => $request->session_duration,
            ],
        ]);

        return response()->json([
            'success' => true,
            'workout_plan' => $workoutPlan,
        ], 201);
    }

    /**
     * Get user's active workout plan
     */
    public function getActivePlan(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $plan = $user->activeWorkoutPlan()
            ->with('planExercises.exercise')
            ->first();

        if (!$plan) {
            return response()->json([
                'has_plan' => false,
                'message' => 'No active workout plan'
            ]);
        }

        return response()->json([
            'has_plan' => true,
            'plan' => $plan,
        ]);
    }

    /**
     * Get today's workout from active plan
     */
    public function getTodaysWorkout(Request $request)
    {
        $user = User::find($request->user_id);
        $today = strtolower(now()->format('l')); // "monday"

        $plan = $user->activeWorkoutPlan()->first();

        if (!$plan) {
            return response()->json([
                'has_workout' => false,
                'message' => 'No active plan'
            ]);
        }

        $exercises = $plan->exercisesForDay($today);

        return response()->json([
            'has_workout' => $exercises->isNotEmpty(),
            'day' => $today,
            'plan_name' => $plan->name,
            'exercises' => $exercises,
        ]);
    }
}
