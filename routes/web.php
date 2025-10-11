<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::get('/onboarding', function () {
    return Inertia::render('onboarding');
})->name('onboarding');

Route::post('/onboarding', [UserController::class, 'store'])->name('onboarding.store');

Route::get('/workout-plan-chat', function () {
    return Inertia::render('workoutPlanChat');
})->middleware('auth')->name('workout-plan-chat');

// Workout plan routes for Inertia frontend (with CSRF protection)
Route::middleware('auth')->group(function () {
    Route::post('/workout-plans', [\App\Http\Controllers\WorkoutPlanController::class, 'store'])
        ->name('workout-plans.store');
    Route::put('/workout-plans/{workoutPlan}/reorder', [\App\Http\Controllers\WorkoutPlanController::class, 'reorder'])
        ->name('workout-plans.reorder');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
