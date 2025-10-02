<?php

namespace App\Models;

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
        'aliases' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}
