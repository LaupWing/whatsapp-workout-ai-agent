<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversation;
use App\Models\AiInteraction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdkAgentService
{
    private string $adkUrl;
    private string $apiKey;
    private string $apiAppName;

    public function __construct()
    {
        $this->adkUrl = config('services.adk.api_url');
        $this->apiKey = config('services.adk.api_key');
        $this->apiAppName = config('services.adk.api_app_name');
    }

    /**
     * Send user message to ADK agent and process response
     */
    public function processMessage(User $user, Conversation $conversation): void
    {
        $startTime = microtime(true);

        try {
            // Get user context (recent workouts, goals, etc.)
            $context = $this->getUserContext($user);

            // Call ADK API
            $response = Http::timeout(30)
                ->post("{$this->adkUrl}/run", [
                    'user_id' => $user->id,
                    'session_id' => $user->whatsapp_number,
                    'state' => [
                        'user_id' => $user->id,
                    ],
                    'message' => $conversation->message_content,
                    'context' => $context,
                    'stream' => false,
                    'app_name' => $this->apiAppName,
                ]);

            if (!$response->successful()) {
                throw new \Exception('ADK API error: ' . $response->body());
            }

            $data = $response->json();
            $responseTime = (microtime(true) - $startTime) * 1000; // ms

            // Save AI interaction
            $aiInteraction = AiInteraction::create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'agent_name' => $data['agent_name'] ?? 'unknown',
                'user_input' => $conversation->message_content,
                'agent_response' => $data['response'],
                'parsed_intent' => $data['intent'] ?? null,
                'context_data' => $context,
                'model_used' => $data['model'] ?? 'gemini-2.0-flash',
                'tokens_used' => $data['tokens_used'] ?? null,
                'response_time_ms' => $responseTime,
                'was_successful' => true,
                'tool_calls' => $data['tool_calls'] ?? null,
            ]);

            // Send response back to user via WhatsApp
            // app(WhatsAppService::class)->sendMessage(
            //     $user->whatsapp_number,
            //     $data['response']
            // );
        } catch (\Exception $e) {
            Log::error('ADK processing error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Log failed interaction
            AiInteraction::create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'agent_name' => 'error',
                'user_input' => $conversation->message_content,
                'agent_response' => '',
                'was_successful' => false,
                'error_message' => $e->getMessage(),
                'response_time_ms' => (microtime(true) - $startTime) * 1000,
            ]);

            // Send error message to user
            // app(WhatsAppService::class)->sendMessage(
            //     $user->whatsapp_number,
            //     "Sorry, I'm having trouble processing that. Please try again in a moment."
            // );
        }
    }

    /**
     * Get user context for AI agent
     */
    private function getUserContext(User $user): array
    {
        return [
            'user_profile' => [
                'name' => $user->name,
                'fitness_goal' => $user->fitness_goal,
                'experience_level' => $user->experience_level,
                'current_weight' => $user->current_weight_kg,
                'target_weight' => $user->target_weight_kg,
            ],
            'recent_workouts' => $user->workouts()
                ->with('workoutExercises.exercise')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($workout) {
                    return [
                        'date' => $workout->workout_date->format('Y-m-d'),
                        'type' => $workout->workout_type,
                        'total_volume' => $workout->total_volume_kg,
                        'exercises' => $workout->workoutExercises->map(fn($we) => [
                            'name' => $we->exercise->name,
                            'sets' => $workout->workoutExercises->where('exercise_id', $we->exercise_id)->count(),
                            'reps' => $we->reps,
                            'weight' => $we->weight_kg,
                        ])->unique('name')->values(),
                    ];
                }),
            'streak' => $user->streak_days,
            'last_workout' => $user->last_workout_date?->format('Y-m-d'),
        ];
    }
}
