<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id',
        'exercise_id',
        'set_number',
        'reps',
        'weight_kg',
        'duration_seconds',
        'distance_km',
        'rpe',
        'notes',
        'is_warmup',
        'is_pr',
    ];

    protected $casts = [
        'is_warmup' => 'boolean',
        'is_pr' => 'boolean',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
