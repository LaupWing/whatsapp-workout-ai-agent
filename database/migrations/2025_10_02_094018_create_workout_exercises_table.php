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
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('set_number'); // 1, 2, 3, etc.
            $table->integer('reps')->nullable(); // Number of repetitions
            $table->decimal('weight_kg', 6, 2)->nullable(); // 80.50 kg
            $table->integer('duration_seconds')->nullable(); // For timed exercises (planks)
            $table->decimal('distance_km', 6, 2)->nullable(); // For cardio (running 5.5km)
            $table->integer('rpe')->nullable(); // Rate of Perceived Exertion (1-10)
            $table->text('notes')->nullable(); // "Felt easy", "Lower back hurt", etc.
            $table->boolean('is_warmup')->default(false);
            $table->boolean('is_pr')->default(false); // Personal Record flag
            $table->timestamps();

            $table->index(['workout_id', 'exercise_id']);
            $table->index('is_pr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
