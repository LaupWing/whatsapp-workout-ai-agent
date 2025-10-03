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
        Schema::create('workout_plan_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week'); // "monday", "tuesday", etc.
            $table->integer('order')->default(0); // Order within the day
            $table->integer('target_sets');
            $table->string('target_reps'); // "8-10", "5", "AMRAP"
            $table->decimal('target_weight_kg', 6, 2)->nullable();
            $table->integer('rest_seconds')->nullable(); // Rest between sets
            $table->text('notes')->nullable(); // "Focus on form", "Increase by 2.5kg each week"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_plan_exercises');
    }
};
