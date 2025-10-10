<?php

use App\Enums\WorkoutPlanGoal;
use App\Enums\WorkoutPlanStatus;
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
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "12-Week Strength Program", "PPL Split"
            $table->text('description')->nullable();
            $table->enum('goal', WorkoutPlanGoal::values());
            $table->integer('duration_weeks')->nullable(); // How many weeks is the program
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', WorkoutPlanStatus::values())
                ->default(WorkoutPlanStatus::ACTIVE->value);
            $table->json('schedule')->nullable(); // {"monday": "push", "wednesday": "pull", "friday": "legs"}
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_plans');
    }
};
