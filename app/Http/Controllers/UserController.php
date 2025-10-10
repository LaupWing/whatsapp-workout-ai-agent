<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        logger('incoming message');
        $validated = $request->validate([
            'whatsapp_number' => 'required|string|unique:users,whatsapp_number',
            'email' => 'required|email|unique:users,email',
            'name' => 'nullable|string|max:255',
            'gender' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:120',
            'height' => 'nullable|numeric|min:1',
            'current_weight' => 'nullable|numeric|min:1',
            'target_weight' => 'nullable|numeric|min:1',
            'fitness_goal' => 'nullable|string',
            'experience_level' => 'required|string',
            'training_location' => 'nullable|string',
            'workout_days' => 'nullable|array',
            'reminder_time' => 'nullable|string',
            'receive_motivation_messages' => 'boolean',
            'consent_whatsapp' => 'required|boolean|accepted',
            'consent_data_usage' => 'required|boolean|accepted',
        ]);

        // Create user
        $user = User::create([
            'whatsapp_number' => $request->whatsapp_number,
            'email' => $request->email,
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'height_cm' => $request->height,
            'current_weight_kg' => $request->current_weight,
            'target_weight_kg' => $request->target_weight,
            'fitness_goal' => $request->fitness_goal,
            'experience_level' => $request->experience_level,
            'training_location' => $request->training_location,
            'workout_days' => $request->workout_days,
            'preferred_reminder_time' => $request->reminder_time,
            'receive_motivation' => $request->receive_motivation_messages ?? true,
            'whatsapp_consent' => $request->consent_whatsapp,
            'data_consent' => $request->consent_data_usage,
            'onboarded_at' => now(),
            'is_active' => true,
        ]);

        // Create Sanctum token for the user (stored in database, not returned)
        $user->createToken('onboarding-token');

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        return to_route('workout-plan-chat');
    }
}
