<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdkAgentService;
use App\Models\Conversation;
use Illuminate\Console\Command;

class TestAdkAgent extends Command
{
    protected $signature = 'adk:test {user_id?} {message?}';
    protected $description = 'Test ADK agent with a user message';

    public function handle(AdkAgentService $adkService): int
    {
        $userId = $this->argument('user_id') ?? 2; // Default to Sarah
        $message = $this->argument('message') ?? 'What did I do yesterday?';

        $user = User::find($userId);
        logger($user);
        if (!$user) {
            $this->error("❌ User {$userId} not found");
            return 1;
        }

        $this->info("👤 User: {$user->name} ({$user->whatsapp_number})");
        $this->info("💬 Message: {$message}");
        $this->info('');
        $this->info('⏳ Calling ADK agent...');
        $this->newLine();

        // Create fake conversation record
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'whatsapp_message_id' => 'test_' . now()->timestamp,
            'direction' => 'incoming',
            'message_type' => 'text',
            'message_content' => $message,
        ]);

        try {
            $startTime = microtime(true);

            // Process with ADK
            $adkService->processMessage($user, $conversation);

            $duration = round((microtime(true) - $startTime) * 1000);

            // Get the AI response
            $aiInteraction = $user->aiInteractions()->latest()->first();

            $this->newLine();
            $this->info('✅ Response received!');
            $this->info("⏱️  Response time: {$duration}ms");
            $this->newLine();

            if ($aiInteraction) {
                $this->line('🤖 Agent: ' . $aiInteraction->agent_name);
                $this->line('📝 Response:');
                $this->line('─────────────────────────────────────');
                $this->line($aiInteraction->agent_response);
                $this->line('─────────────────────────────────────');
                $this->newLine();

                if ($aiInteraction->tokens_used) {
                    $this->line("🪙 Tokens used: {$aiInteraction->tokens_used}");
                }

                if ($aiInteraction->tool_calls) {
                    $this->line('🔧 Tools called: ' . json_encode($aiInteraction->tool_calls));
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
