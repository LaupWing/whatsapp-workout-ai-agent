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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bench Press, Squat, etc.
            $table->json('aliases')->nullable(); // ['bench', 'bp', 'chest press'] for NLP matching
            $table->enum('category', ['strength', 'cardio', 'flexibility', 'sports'])->default('strength');
            $table->string('muscle_group'); // chest, legs, back, shoulders, arms, core, full_body
            $table->enum('equipment', ['barbell', 'dumbbell', 'machine', 'bodyweight', 'cable', 'other'])->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable(); // YouTube link for form demonstration
            $table->json('tags')->nullable(); // ['compound', 'push', 'horizontal'] for filtering
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
