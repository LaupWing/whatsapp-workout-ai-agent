<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Services\AdkAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private AdkAgentService $adkService
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
            $this->handleIncomingMessage($value);
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

        // Get or create user
        $user = User::firstOrCreate(
            ['whatsapp_number' => $from],
            ['is_active' => true]
        );

        // Save incoming message
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'whatsapp_message_id' => $messageId,
            'direction' => 'incoming',
            'message_type' => $messageType,
            'message_content' => $message[$messageType]['body'] ?? null,
            'whatsapp_metadata' => $message,
        ]);

        // Send to ADK agent for processing
        if ($messageType === 'text') {
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
