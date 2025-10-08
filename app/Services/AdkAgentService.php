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
        $this->apiAppName = config('services.adk.api_app_name');
    }

    private function ensureSession(User $user, string $sessionId): void
    {
        // Try to get session
        $response = Http::get(
            "{$this->adkUrl}/apps/workout_coach_agent/users/{$user->id}/sessions/{$sessionId}"
        );

        // If session doesn't exist (404), create it
        if ($response->status() === 404) {
            Http::post(
                "{$this->adkUrl}/apps/workout_coach_agent/users/{$user->id}/sessions/{$sessionId}",
                [
                    'user_id' => $user->id,

                ]
            );
        }
    }

    /**
     * Send user message to ADK agent and process response
     */
    public function processMessage(User $user, Conversation $conversation): void
    {
        $startTime = microtime(true);

        try {
            $context = $this->getUserContext($user);
            $sessionId = $user->whatsapp_number;
            $this->ensureSession($user, $sessionId);
            // Call ADK API
            $response = Http::timeout(30)
                ->post("{$this->adkUrl}/run", [
                    'app_name' => $this->apiAppName,
                    'user_id' => (string) $user->id,
                    'session_id' => (string) $sessionId,
                    'new_message' => [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $conversation->message_content]
                        ]
                    ],
                    'streaming' => false
                ]);

            if (!$response->successful()) {
                logger()->error('ADK API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('ADK API error: ' . $response->body());
            }

            $events = $response->json(); // Array of events
            $responseTime = (microtime(true) - $startTime) * 1000;

            // ✅ EXTRACT THE FINAL TEXT RESPONSE
            $finalResponse = $this->extractFinalResponse($events);
            $agentName = $this->extractAgentName($events);
            $toolCalls = $this->extractToolCalls($events);

            $aiInteraction = AiInteraction::create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'agent_name' => $agentName,
                'user_input' => $conversation->message_content,
                'agent_response' => $finalResponse,
                'parsed_intent' => null, // Could extract from events if needed
                'context_data' => $context,
                'model_used' => 'gemini-2.0-flash',
                'tokens_used' => $this->extractTokenCount($events),
                'response_time_ms' => $responseTime,
                'was_successful' => true,
                'tool_calls' => $toolCalls,
            ]);

            // Send response back to user via WhatsApp
            app(WhatsAppService::class)->sendMessage(
                '31654754116',
                $aiInteraction->agent_response
            );
        } catch (\Exception $e) {
            Log::error('ADK processing error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

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
            app(WhatsAppService::class)->sendMessage(
                $user->whatsapp_number,
                "Sorry, I'm having trouble processing that. Please try again in a moment."
            );
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

    /**
     * Extract the final text response from ADK events
     */
    private function extractFinalResponse(array $events): string
    {
        // Loop backwards to find the last text response from the model
        for ($i = count($events) - 1; $i >= 0; $i--) {
            $event = $events[$i];

            if (
                isset($event['content']['role']) &&
                $event['content']['role'] === 'model' &&
                isset($event['content']['parts'])
            ) {
                foreach ($event['content']['parts'] as $part) {
                    if (isset($part['text'])) {
                        return $part['text'];
                    }
                }
            }
        }

        return 'No response generated';
    }

    /**
     * Extract agent name from events
     */
    private function extractAgentName(array $events): string
    {
        foreach ($events as $event) {
            if (isset($event['author'])) {
                return $event['author'];
            }
        }

        return 'unknown';
    }

    /**
     * Extract tool calls from events
     */
    private function extractToolCalls(array $events): ?array
    {
        $toolCalls = [];

        foreach ($events as $event) {
            if (isset($event['content']['parts'])) {
                foreach ($event['content']['parts'] as $part) {
                    if (isset($part['functionCall'])) {
                        $toolCalls[] = [
                            'name' => $part['functionCall']['name'],
                            'args' => $part['functionCall']['args'] ?? null,
                        ];
                    }
                }
            }
        }

        return !empty($toolCalls) ? $toolCalls : null;
    }

    /**
     * Extract token count from events
     */
    private function extractTokenCount(array $events): ?int
    {
        // Find the last event with usage metadata
        for ($i = count($events) - 1; $i >= 0; $i--) {
            if (isset($events[$i]['usageMetadata']['totalTokenCount'])) {
                return $events[$i]['usageMetadata']['totalTokenCount'];
            }
        }

        return null;
    }
}
