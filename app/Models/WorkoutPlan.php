<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'goal',
        'duration_weeks',
        'start_date',
        'end_date',
        'status',
        'schedule',
        'notes',
    ];

    protected $casts = [
        'schedule' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planExercises()
    {
        return $this->hasMany(WorkoutPlanExercise::class);
    }

    public function exercisesForDay(string $day)
    {
        return $this->planExercises()
            ->where('day_of_week', strtolower($day))
            ->orderBy('order')
            ->with('exercise')
            ->get();
    }
}
