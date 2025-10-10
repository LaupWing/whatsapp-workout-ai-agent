<?php

namespace App\Models;

use App\Enums\Equipment;
use App\Enums\ExerciseCategory;
use App\Enums\ExerciseDifficulty;
use App\Enums\MuscleGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'aliases',
        'category',
        'muscle_group',
        'equipment',
        'difficulty',
        'description',
        'video_url',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'category' => ExerciseCategory::class,
        'muscle_group' => MuscleGroup::class,
        'equipment' => Equipment::class,
        'difficulty' => ExerciseDifficulty::class,
        'aliases' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}
