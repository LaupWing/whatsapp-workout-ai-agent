<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutPlanExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_plan_id',
        'exercise_id',
        'day_of_week',
        'order',
        'target_sets',
        'target_reps',
        'target_weight_kg',
        'rest_seconds',
        'notes',
    ];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
