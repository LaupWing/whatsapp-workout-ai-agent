<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'workout_type',
        'notes',
        'total_volume_kg',
        'total_sets',
        'energy_level',
        'rating',
    ];

    protected $casts = [
        'workout_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}
