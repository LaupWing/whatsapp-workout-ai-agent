<?php

namespace App\Http\Controllers;

use App\Enums\WorkoutPlanGoal;
use App\Enums\WorkoutPlanStatus;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Services\WorkoutPlanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    /**
     * Generate a new AI-powered workout plan
     */
    public function generate(Request $request, WorkoutPlanGeneratorService $generatorService)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'workout_days' => 'required|array|min:1|max:7',
            'workout_days.*' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'muscle_groups' => 'required|array|min:1',
            'muscle_groups.*' => 'required|string',
            'focus_muscles' => 'nullable|array',
            'focus_muscles.*' => 'string',
            'session_duration' => 'required|integer|min:15|max:180',
            'goal' => 'nullable|string|in:strength,hypertrophy,endurance,weight_loss,general_fitness',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        try {
            // Deactivate current active plans
            WorkoutPlan::where('user_id', $user->id)
                ->where('status', WorkoutPlanStatus::ACTIVE)
                ->update(['status' => WorkoutPlanStatus::COMPLETED]);

            // Parse goal enum if provided
            $goal = $request->goal
                ? WorkoutPlanGoal::from($request->goal)
                : null;

            // Generate workout plan
            $workoutPlan = $generatorService->generatePlan(
                user: $user,
                workoutDays: $request->workout_days,
                muscleGroups: $request->muscle_groups,
                focusMuscles: $request->focus_muscles,
                sessionDuration: $request->session_duration,
                goal: $goal
            );

            Log::info('Workout plan generated successfully', [
                'user_id' => $user->id,
                'plan_id' => $workoutPlan->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Workout plan generated successfully',
                'workout_plan' => $workoutPlan,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to generate workout plan', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate workout plan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
