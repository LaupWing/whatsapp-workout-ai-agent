<?php

namespace App\Services;

use App\Enums\WorkoutPlanGoal;
use App\Enums\WorkoutPlanStatus;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkoutPlanGeneratorService
{
    /**
     * Generate a workout plan using OpenAI based on user preferences
     */
    public function generatePlan(
        User $user,
        array $workoutDays,
        array $muscleGroups,
        ?array $focusMuscles = null,
        int $sessionDuration = 60,
        ?WorkoutPlanGoal $goal = null
    ): WorkoutPlan {
        // Get available exercises with muscle groups
        $availableExercises = $this->getAvailableExercises($muscleGroups);

        // Build OpenAI prompt
        $planData = $this->generatePlanWithAI(
            $workoutDays,
            $muscleGroups,
            $focusMuscles ?? $muscleGroups,
            $sessionDuration,
            $availableExercises,
            $goal
        );

        // Create workout plan
        $workoutPlan = WorkoutPlan::create([
            'user_id' => $user->id,
            'name' => $this->generatePlanName($goal, $muscleGroups),
            'description' => $this->generatePlanDescription($workoutDays, $sessionDuration, $goal),
            'goal' => $goal ?? WorkoutPlanGoal::GENERAL_FITNESS,
            'status' => WorkoutPlanStatus::ACTIVE,
            'duration_weeks' => 4, // Default 4-week plan
            'start_date' => now(),
            'end_date' => now()->addWeeks(4),
            'schedule' => [
                'workout_days' => $workoutDays,
                'muscle_groups' => $muscleGroups,
                'focus_muscles' => $focusMuscles,
                'session_duration' => $sessionDuration,
            ],
        ]);

        // Create exercises for each day
        $this->createPlanExercises($workoutPlan, $planData);

        return $workoutPlan->load('planExercises.exercise');
    }

    /**
     * Get available exercises filtered by muscle groups
     */
    private function getAvailableExercises(array $muscleGroups): array
    {
        return Exercise::query()
            ->whereIn('muscle_group', $muscleGroups)
            ->where('is_active', true)
            ->get()
            ->map(function ($exercise) {
                return [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'muscle_group' => $exercise->muscle_group->value,
                    'difficulty' => $exercise->difficulty->value ?? 'intermediate',
                ];
            })
            ->toArray();
    }

    /**
     * Generate workout plan using OpenAI
     */
    private function generatePlanWithAI(
        array $workoutDays,
        array $muscleGroups,
        array $focusMuscles,
        int $sessionDuration,
        array $availableExercises,
        ?WorkoutPlanGoal $goal
    ): array {
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Determine sets and reps based on goal
        [$minSets, $maxSets, $minReps, $maxReps] = $this->getSetRepRanges($goal);

        // Build JSON schema for structured output
        $jsonSchema = $this->buildJsonSchema($daysOfWeek, $minSets, $maxSets, $minReps, $maxReps);

        $systemPrompt = $this->buildSystemPrompt(
            $availableExercises,
            $minSets,
            $maxSets,
            $minReps,
            $maxReps
        );

        $userPrompt = $this->buildUserPrompt(
            $workoutDays,
            $muscleGroups,
            $focusMuscles,
            $sessionDuration,
            $goal
        );

        // Make OpenAI request with retry logic
        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'workout_plan',
                            'strict' => true,
                            'schema' => $jsonSchema,
                        ],
                    ],
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                ]);

                if (!$response->successful()) {
                    throw new \Exception('OpenAI API request failed: ' . $response->body());
                }

                $responseData = json_decode(
                    $response->json()['choices'][0]['message']['content'],
                    true
                );

                // Validate response structure
                if ($this->validatePlanStructure($responseData, $workoutDays)) {
                    return $this->normalizeResponse($responseData);
                }

                Log::warning('Invalid plan structure from OpenAI', ['attempt' => $attempt + 1]);
                $attempt++;
            } catch (\Exception $e) {
                Log::error('OpenAI API error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);
                $attempt++;

                if ($attempt >= $maxRetries) {
                    throw $e;
                }
            }
        }

        throw new \Exception('Failed to generate workout plan after ' . $maxRetries . ' attempts');
    }

    /**
     * Get set and rep ranges based on goal
     */
    private function getSetRepRanges(?WorkoutPlanGoal $goal): array
    {
        return match ($goal) {
            WorkoutPlanGoal::STRENGTH => [4, 6, 3, 6],        // Heavy weight, low reps
            WorkoutPlanGoal::HYPERTROPHY => [3, 5, 8, 12],    // Moderate weight, moderate reps
            WorkoutPlanGoal::ENDURANCE => [2, 4, 15, 25],     // Light weight, high reps
            WorkoutPlanGoal::WEIGHT_LOSS => [3, 4, 12, 20],   // Circuit-style training
            default => [3, 5, 8, 15],                          // General fitness
        };
    }

    /**
     * Build JSON schema for structured output
     */
    private function buildJsonSchema(array $daysOfWeek, int $minSets, int $maxSets, int $minReps, int $maxReps): array
    {
        $dayProperties = [];
        foreach ($daysOfWeek as $day) {
            $dayProperties[$day] = [
                'anyOf' => [
                    [
                        'type' => 'string',
                        'const' => 'Rest day',
                    ],
                    [
                        'type' => 'object',
                        'properties' => [
                            'mainFocus' => [
                                'type' => 'string',
                                'description' => 'Main muscle groups targeted on this day',
                            ],
                            'exercises' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'exercise_id' => [
                                            'type' => 'integer',
                                            'description' => 'ID of the exercise from available exercises',
                                        ],
                                        'sets' => [
                                            'type' => 'integer',
                                            'minimum' => $minSets,
                                            'maximum' => $maxSets,
                                        ],
                                        'reps' => [
                                            'type' => 'integer',
                                            'minimum' => $minReps,
                                            'maximum' => $maxReps,
                                        ],
                                    ],
                                    'required' => ['exercise_id', 'sets', 'reps'],
                                    'additionalProperties' => false,
                                ],
                                'minItems' => 1,
                            ],
                        ],
                        'required' => ['mainFocus', 'exercises'],
                        'additionalProperties' => false,
                    ],
                ],
            ];
        }

        return [
            'type' => 'object',
            'properties' => $dayProperties,
            'required' => $daysOfWeek,
            'additionalProperties' => false,
        ];
    }

    /**
     * Build system prompt for OpenAI
     */
    private function buildSystemPrompt(
        array $availableExercises,
        int $minSets,
        int $maxSets,
        int $minReps,
        int $maxReps
    ): string {
        $exercisesJson = json_encode($availableExercises, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a professional fitness coach creating personalized workout plans.

Available Exercises:
{$exercisesJson}

Programming Guidelines:
- Sets: {$minSets}-{$maxSets} per exercise
- Reps: {$minReps}-{$maxReps} per exercise
- Use ONLY exercise_id from the available exercises list

Training Principles:
1. Progressive overload: Start with compound exercises, end with isolation
2. Muscle balance: Ensure balanced development to prevent imbalances
3. Recovery: Don't train the same muscle groups on consecutive days
4. Exercise selection: Choose exercises appropriate for the target muscle groups
5. Volume distribution: Adjust number of exercises based on session duration
6. Safety first: Include appropriate warm-up movements

For rest days, use the string "Rest day".
For workout days, provide a mainFocus describing the primary muscle groups and a list of exercises.
PROMPT;
    }

    /**
     * Build user prompt for OpenAI
     */
    private function buildUserPrompt(
        array $workoutDays,
        array $muscleGroups,
        array $focusMuscles,
        int $sessionDuration,
        ?WorkoutPlanGoal $goal
    ): string {
        $workoutDaysStr = implode(', ', $workoutDays);
        $muscleGroupsStr = implode(', ', $muscleGroups);
        $focusMusclesStr = implode(', ', $focusMuscles);
        $goalStr = $goal?->label() ?? 'general fitness';

        return <<<PROMPT
Create a workout plan with:
- Training days: {$workoutDaysStr} (rest on other days)
- Target muscle groups: {$muscleGroupsStr}
- Primary focus: {$focusMusclesStr}
- Session duration: {$sessionDuration} minutes
- Goal: {$goalStr}

IMPORTANT: Only include workout days on: {$workoutDaysStr}. All other days should be "Rest day".
PROMPT;
    }

    /**
     * Validate plan structure
     */
    private function validatePlanStructure(array $responseData, array $workoutDays): bool
    {
        // Convert workout days to lowercase for comparison
        $workoutDaysLower = array_map('strtolower', $workoutDays);

        // Check if at least one workout day has exercises
        foreach ($workoutDaysLower as $day) {
            if (isset($responseData[$day]) && is_array($responseData[$day]) && isset($responseData[$day]['exercises'])) {
                return true;
            }

            // Also check capitalized version
            $capitalizedDay = ucfirst($day);
            if (isset($responseData[$capitalizedDay]) && is_array($responseData[$capitalizedDay]) && isset($responseData[$capitalizedDay]['exercises'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize response to ensure consistent structure
     */
    private function normalizeResponse(array $responseData): array
    {
        // Convert all keys to lowercase
        $normalized = array_change_key_case($responseData, CASE_LOWER);
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Ensure all days exist
        foreach ($daysOfWeek as $day) {
            if (!array_key_exists($day, $normalized)) {
                $normalized[$day] = 'Rest day';
            }

            // Skip if rest day
            if (is_string($normalized[$day])) {
                continue;
            }

            // Ensure exercises have correct data types
            if (is_array($normalized[$day]) && isset($normalized[$day]['exercises'])) {
                foreach ($normalized[$day]['exercises'] as &$exercise) {
                    $exercise['sets'] = (int) $exercise['sets'];
                    $exercise['exercise_id'] = (int) $exercise['exercise_id'];

                    // Handle reps (could be number or string like "12-15")
                    if (is_numeric($exercise['reps'])) {
                        $exercise['reps'] = (int) $exercise['reps'];
                    } else {
                        // Extract first number from string
                        preg_match('/\d+/', $exercise['reps'], $matches);
                        $exercise['reps'] = isset($matches[0]) ? (int) $matches[0] : 10;
                    }
                }
            }
        }

        return $normalized;
    }

    /**
     * Create plan exercises from AI response
     */
    private function createPlanExercises(WorkoutPlan $workoutPlan, array $planData): void
    {
        foreach ($planData as $day => $workoutData) {
            // Skip rest days
            if ($workoutData === 'Rest day' || !isset($workoutData['exercises'])) {
                continue;
            }

            foreach ($workoutData['exercises'] as $index => $exercise) {
                $workoutPlan->planExercises()->create([
                    'exercise_id' => $exercise['exercise_id'],
                    'day_of_week' => $day,
                    'order' => $index + 1,
                    'target_sets' => $exercise['sets'],
                    'target_reps' => $exercise['reps'],
                    'rest_seconds' => $this->calculateRestTime($exercise['reps']),
                ]);
            }
        }
    }

    /**
     * Calculate rest time based on reps
     */
    private function calculateRestTime(int $reps): int
    {
        // Heavier sets (low reps) need more rest
        if ($reps <= 6) {
            return 180; // 3 minutes for strength
        } elseif ($reps <= 12) {
            return 90;  // 90 seconds for hypertrophy
        } else {
            return 60;  // 60 seconds for endurance
        }
    }

    /**
     * Generate plan name
     */
    private function generatePlanName(?WorkoutPlanGoal $goal, array $muscleGroups): string
    {
        $goalLabel = $goal?->label() ?? 'Custom';
        $muscleGroupsStr = count($muscleGroups) > 2
            ? 'Full Body'
            : implode(' & ', array_map(fn($mg) => ucfirst($mg), $muscleGroups));

        return "{$goalLabel} - {$muscleGroupsStr} Plan";
    }

    /**
     * Generate plan description
     */
    private function generatePlanDescription(array $workoutDays, int $sessionDuration, ?WorkoutPlanGoal $goal): string
    {
        $daysCount = count($workoutDays);
        $goalLabel = $goal?->label() ?? 'general fitness';

        return "AI-generated {$daysCount}-day workout plan focused on {$goalLabel}. Each session is approximately {$sessionDuration} minutes.";
    }
}
