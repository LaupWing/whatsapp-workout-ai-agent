<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private string $phoneNumberId;
    private string $apiVersion;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->apiVersion = config('services.whatsapp.api_version');
    }

    /**
     * Send text message to WhatsApp user
     */
    public function sendMessage(string $to, string $message): ?string
    {
        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('WhatsApp send error', [
                    'to' => $to,
                    'response' => $response->json(),
                ]);
                return null;
            }

            $data = $response->json();
            $messageId = $data['messages'][0]['id'] ?? null;

            // Save outgoing message
            $user = User::where('whatsapp_number', $to)->first();
            if ($user && $messageId) {
                Conversation::create([
                    'user_id' => $user->id,
                    'whatsapp_message_id' => $messageId,
                    'direction' => 'outgoing',
                    'message_type' => 'text',
                    'message_content' => $message,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'whatsapp_metadata' => $data,
                ]);
            }

            return $messageId;
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send message with quick reply buttons
     */
    public function sendInteractiveButtons(string $to, string $bodyText, array $buttons): ?string
    {
        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'interactive',
                    'interactive' => [
                        'type' => 'button',
                        'body' => [
                            'text' => $bodyText,
                        ],
                        'action' => [
                            'buttons' => array_map(function ($button, $index) {
                                return [
                                    'type' => 'reply',
                                    'reply' => [
                                        'id' => $button['id'] ?? "btn_{$index}",
                                        'title' => $button['title'],
                                    ],
                                ];
                            }, $buttons, array_keys($buttons)),
                        ],
                    ],
                ]);

            return $response->json()['messages'][0]['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('WhatsApp interactive buttons error', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send template message (for proactive notifications)
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): ?string
    {
        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en',
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => array_map(fn($p) => ['type' => 'text', 'text' => $p], $parameters),
                            ],
                        ],
                    ],
                ]);

            return $response->json()['messages'][0]['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('WhatsApp template error', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download media file from WhatsApp
     */
    public function downloadMedia(string $mediaId): ?string
    {
        try {
            // Step 1: Get media URL from media ID
            $mediaInfoResponse = Http::withToken($this->token)
                ->get("https://graph.facebook.com/{$this->apiVersion}/{$mediaId}");

            if (!$mediaInfoResponse->successful()) {
                Log::error('WhatsApp media info error', [
                    'media_id' => $mediaId,
                    'response' => $mediaInfoResponse->json(),
                ]);
                return null;
            }

            $mediaUrl = $mediaInfoResponse->json()['url'] ?? null;
            if (!$mediaUrl) {
                return null;
            }

            // Step 2: Download the actual media file
            $mediaResponse = Http::withToken($this->token)
                ->get($mediaUrl);

            if (!$mediaResponse->successful()) {
                Log::error('WhatsApp media download error', [
                    'media_url' => $mediaUrl,
                ]);
                return null;
            }

            // Step 3: Save to temporary file
            $tempPath = storage_path('app/temp/voice_' . $mediaId . '.ogg');

            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            file_put_contents($tempPath, $mediaResponse->body());

            return $tempPath;
        } catch (\Exception $e) {
            Log::error('WhatsApp media download exception', [
                'media_id' => $mediaId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
