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
     * Store a new workout plan (with generation)
     */
    public function store(Request $request, WorkoutPlanGeneratorService $generatorService)
    {
        $validator = Validator::make($request->all(), [
            'goal' => 'required|string|in:strength,hypertrophy,endurance,weight_loss,general_fitness',
            'muscle_groups' => 'required|array|min:1',
            'muscle_groups.*' => 'string',
            'primary_focus' => 'nullable|string',
            'session_duration' => 'required|integer|min:15|max:180',
            'workout_days' => 'required|array|min:1|max:7',
            'workout_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            // Deactivate current active plans
            WorkoutPlan::where('user_id', $user->id)
                ->where('status', WorkoutPlanStatus::ACTIVE)
                ->update(['status' => WorkoutPlanStatus::COMPLETED]);

            // Parse goal enum
            $goal = WorkoutPlanGoal::from($request->goal);

            // Determine focus muscles
            $focusMuscles = $request->primary_focus && $request->primary_focus !== 'No Preference'
                ? [$request->primary_focus]
                : $request->muscle_groups;

            // Generate workout plan using AI
            $workoutPlan = $generatorService->generatePlan(
                user: $user,
                workoutDays: $request->workout_days,
                muscleGroups: $request->muscle_groups,
                focusMuscles: $focusMuscles,
                sessionDuration: $request->session_duration,
                goal: $goal
            );

            Log::info('Workout plan generated for user', [
                'user_id' => $user->id,
                'plan_id' => $workoutPlan->id,
            ]);

            // Return JSON with the generated plan
            return response()->json([
                'success' => true,
                'message' => 'Workout plan generated successfully',
                'workout_plan' => $workoutPlan->load('planExercises.exercise')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to generate workout plan', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate workout plan: ' . $e->getMessage()
            ], 500);
        }
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
     * Reorder exercises in a workout plan (for drag and drop)
     */
    public function reorder(Request $request, WorkoutPlan $workoutPlan)
    {
        $validator = Validator::make($request->all(), [
            'exercises' => 'required|array|min:1',
            'exercises.*.id' => 'required|integer|exists:workout_plan_exercises,id',
            'exercises.*.day_of_week' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'exercises.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify user owns this plan
        if ($request->user()->id !== $workoutPlan->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        try {
            // Update each exercise
            foreach ($request->exercises as $exerciseData) {
                $workoutPlan->planExercises()
                    ->where('id', $exerciseData['id'])
                    ->update([
                        'day_of_week' => $exerciseData['day_of_week'],
                        'order' => $exerciseData['order'],
                    ]);
            }

            Log::info('Workout plan reordered', [
                'user_id' => $request->user()->id,
                'plan_id' => $workoutPlan->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Workout plan updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reorder workout plan', [
                'user_id' => $request->user()->id,
                'plan_id' => $workoutPlan->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update workout plan'
            ], 500);
        }
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
