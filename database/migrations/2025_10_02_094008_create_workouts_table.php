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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('workout_date'); // When they worked out
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Calculated or user-provided
            $table->string('workout_type')->nullable(); // push, pull, legs, full_body, cardio
            $table->text('notes')->nullable(); // Overall session notes
            $table->integer('total_volume_kg')->default(0); // Sum of all sets*reps*weight
            $table->integer('total_sets')->default(0);
            $table->enum('energy_level', ['low', 'medium', 'high'])->nullable(); // User reported
            $table->integer('rating')->nullable(); // 1-5 stars, how did they feel?
            $table->timestamps();

            $table->index(['user_id', 'workout_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
