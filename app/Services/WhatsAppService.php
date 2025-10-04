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
}
