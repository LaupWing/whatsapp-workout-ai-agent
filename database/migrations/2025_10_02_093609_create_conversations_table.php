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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('whatsapp_message_id')->unique(); // WhatsApp's message ID (wamid)
            $table->enum('direction', ['incoming', 'outgoing']); // From user or to user
            $table->enum('message_type', ['text', 'image', 'audio', 'video', 'document', 'template']); // WhatsApp message types
            $table->text('message_content')->nullable(); // The actual message text
            $table->string('media_url')->nullable(); // For images, videos, documents
            $table->string('media_mime_type')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent'); // Message delivery status
            $table->json('whatsapp_metadata')->nullable(); // Raw webhook data from WhatsApp
            $table->timestamp('sent_at')->nullable(); // When we sent it (for outgoing)
            $table->timestamp('delivered_at')->nullable(); // WhatsApp delivery confirmation
            $table->timestamp('read_at')->nullable(); // WhatsApp read receipt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
