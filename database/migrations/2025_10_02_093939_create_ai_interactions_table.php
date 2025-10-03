<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');

            // Agent info
            $table->string('agent_name'); // "fitness_coach", "progress_tracker"

            // Conversation
            $table->text('user_input'); // What user said
            $table->text('agent_response'); // Final text response from agent

            // Metadata
            $table->string('model_used')->default('gemini-2.0-flash');
            $table->integer('tokens_used')->nullable(); // From usageMetadata.totalTokenCount
            $table->integer('response_time_ms')->nullable(); // Calculated

            // Status
            $table->boolean('was_successful')->default(true);
            $table->text('error_message')->nullable();

            // Tool tracking
            $table->json('tool_calls')->nullable(); // Array of {name, args}

            // Raw data for debugging
            $table->json('raw_events')->nullable(); // Full ADK response (optional)

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index('agent_name');
            $table->index('was_successful');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_interactions');
    }
};
