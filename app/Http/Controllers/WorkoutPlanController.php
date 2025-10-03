<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;

class WorkoutPlanController extends Controller
{
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
