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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->string('name');
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            // $table->rememberToken();
            $table->string('whatsapp_number')->unique(); // User's WhatsApp number
            $table->string('name')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->integer('age')->nullable();
            $table->decimal('height_cm', 5, 2)->nullable(); // 175.50 cm
            $table->decimal('current_weight_kg', 5, 2)->nullable(); // 75.50 kg
            $table->decimal('target_weight_kg', 5, 2)->nullable();
            $table->enum('fitness_goal', ['lose_weight', 'build_muscle', 'maintain', 'strength', 'endurance'])->nullable();
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->json('preferences')->nullable(); // Store preferences like workout days, time, etc.
            $table->integer('streak_days')->default(0); // Workout streak
            $table->date('last_workout_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('onboarded_at')->nullable(); // When they completed setup
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
