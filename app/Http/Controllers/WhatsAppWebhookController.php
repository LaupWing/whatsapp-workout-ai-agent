<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Services\AdkAgentService;
use App\Services\WhatsAppService;
use App\Services\VoiceTranscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private AdkAgentService $adkService,
        private WhatsAppService $whatsappService,
        private VoiceTranscriptionService $transcriptionService
    ) {}

    // Webhook verification (required by WhatsApp)
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');
        logger()->info('Verifying WhatsApp webhook', $request->all());
        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    // Receive incoming messages
    public function webhook(Request $request)
    {
        Log::info('WhatsApp webhook received', $request->all());

        $data = $request->all();

        // WhatsApp sends updates in this structure
        if (!isset($data['entry'][0]['changes'][0]['value'])) {
            return response()->json(['status' => 'ignored']);
        }

        $value = $data['entry'][0]['changes'][0]['value'];
        logger()->info('Processing WhatsApp webhook value', $value);


        // Handle different webhook types
        if (isset($value['messages'])) {
            $userExists = User::where('whatsapp_number', $value['from'])->exists();

            if (!$userExists) {
                $this->whatsappService->sendMessage($value['from'], "Hi! It looks like you're new here. Please sign up on our website to get started with your fitness journey! Go to: https://yourwebsite.com/signup");
            } else {
                $this->handleIncomingMessage($value);
            }
        } elseif (isset($value['statuses'])) {
            $this->handleMessageStatus($value);
        }


        return response()->json(['status' => 'ok']);
    }

    private function handleIncomingMessage($value)
    {
        $message = $value['messages'][0];
        $from = $message['from']; // User's WhatsApp number
        $messageId = $message['id'];
        $messageType = $message['type']; // text, image, audio, etc.

        // Check if we've already processed this message (prevent duplicates)
        if (Conversation::where('whatsapp_message_id', $messageId)->exists()) {
            Log::info('Duplicate message detected, skipping', ['message_id' => $messageId]);
            return;
        }

        // Get or create user
        $user = User::firstOrCreate(
            ['whatsapp_number' => $from],
            ['is_active' => true]
        );

        $messageContent = null;

        // Handle voice messages
        if ($messageType === 'audio') {
            $mediaId = $message['audio']['id'] ?? null;

            if ($mediaId) {
                Log::info('Processing voice message', [
                    'media_id' => $mediaId,
                    'user_id' => $user->id,
                ]);

                // Download audio file
                $audioPath = $this->whatsappService->downloadMedia($mediaId);

                if ($audioPath) {
                    // Transcribe audio to text
                    $transcribedText = $this->transcriptionService->transcribe($audioPath);

                    if ($transcribedText) {
                        $messageContent = $transcribedText;
                        Log::info('Voice message transcribed successfully', [
                            'user_id' => $user->id,
                            'text' => $transcribedText,
                        ]);
                        $this->whatsappService->sendMessage(
                            $from,
                            "Your transcription: \"$transcribedText\""
                        );
                    } else {
                        Log::error('Voice transcription failed', [
                            'media_id' => $mediaId,
                            'user_id' => $user->id,
                        ]);

                        // Notify user of transcription failure
                        $this->whatsappService->sendMessage(
                            $from,
                            "Sorry, I couldn't understand your voice message. Please try sending it as text or recording again."
                        );
                        return;
                    }
                } else {
                    Log::error('Voice message download failed', [
                        'media_id' => $mediaId,
                        'user_id' => $user->id,
                    ]);
                    return;
                }
            }
        } elseif ($messageType === 'text') {
            $messageContent = $message['text']['body'] ?? null;
        }

        // Save incoming message
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'whatsapp_message_id' => $messageId,
            'direction' => 'incoming',
            'message_type' => $messageType,
            'message_content' => $messageContent,
            'whatsapp_metadata' => $message,
        ]);

        // Send to ADK agent for processing (both text and transcribed audio)
        if ($messageContent && in_array($messageType, ['text', 'audio'])) {
            $this->adkService->processMessage($user, $conversation);
        }
    }

    private function handleMessageStatus($value)
    {
        // Update message delivery status (delivered, read, etc.)
        $status = $value['statuses'][0];
        $messageId = $status['id'];

        Conversation::where('whatsapp_message_id', $messageId)->update([
            'status' => $status['status'],
            'delivered_at' => $status['timestamp'] ?? null,
        ]);
    }
}
