<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ExperienceLevel;
use App\Enums\FitnessGoal;
use App\Enums\Gender;
use App\Enums\TrainingLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        // 'password',
        'whatsapp_number',
        'name',
        'gender',
        'age',
        'height_cm',
        'current_weight_kg',
        'target_weight_kg',
        'fitness_goal',
        'experience_level',
        'training_location',
        'workout_days',
        'preferred_reminder_time',
        'receive_motivation',
        'whatsapp_consent',
        'data_consent',
        'streak_days',
        'last_workout_date',
        'is_active',
        'onboarded_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
            'gender' => Gender::class,
            'fitness_goal' => FitnessGoal::class,
            'experience_level' => ExperienceLevel::class,
            'training_location' => TrainingLocation::class,
            'workout_days' => 'array',
            'receive_motivation' => 'boolean',
            'whatsapp_consent' => 'boolean',
            'data_consent' => 'boolean',
            'is_active' => 'boolean',
            'last_workout_date' => 'date',
            'onboarded_at' => 'datetime',
        ];
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function progressMetrics()
    {
        return $this->hasMany(ProgressMetric::class);
    }

    public function aiInteractions()
    {
        return $this->hasMany(AIInteraction::class);
    }

    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    public function activeWorkoutPlan()
    {
        return $this->hasOne(WorkoutPlan::class)->where('status', 'active');
    }
}
