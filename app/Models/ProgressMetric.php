<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recorded_date',
        'body_weight_kg',
        'body_fat_percentage',
        'chest_cm',
        'waist_cm',
        'hips_cm',
        'bicep_cm',
        'thigh_cm',
        'progress_photo_url',
        'notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
