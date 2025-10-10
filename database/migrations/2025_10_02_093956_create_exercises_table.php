<?php

use App\Enums\Equipment;
use App\Enums\ExerciseCategory;
use App\Enums\ExerciseDifficulty;
use App\Enums\MuscleGroup;
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
            $table->enum('category', ExerciseCategory::values())->default(ExerciseCategory::STRENGTH->value);
            $table->enum('muscle_group', MuscleGroup::values());
            $table->enum('equipment', Equipment::values())->nullable();
            $table->enum('difficulty', ExerciseDifficulty::values())->default(ExerciseDifficulty::BEGINNER->value);
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
