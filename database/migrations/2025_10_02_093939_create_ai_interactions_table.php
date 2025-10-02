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
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade'); // Link to WhatsApp message
            $table->string('agent_name'); // Which ADK agent handled this (coach, logger, tracker, etc.)
            $table->text('user_input'); // What the user said
            $table->text('agent_response'); // What the AI responded
            $table->json('parsed_intent')->nullable(); // Structured intent: {action: 'log_workout', entities: {...}}
            $table->json('context_data')->nullable(); // Any context used (recent workouts, user goals, etc.)
            $table->string('model_used')->nullable(); // gemini-2.0-flash, gpt-4o, etc.
            $table->integer('tokens_used')->nullable(); // Track API costs
            $table->integer('response_time_ms')->nullable(); // Performance tracking
            $table->boolean('was_successful')->default(true); // Did it work or error out?
            $table->text('error_message')->nullable(); // If it failed, why?
            $table->json('tool_calls')->nullable(); // Which tools/functions were called
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('agent_name');
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
