<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::get('/onboarding', function () {
    return Inertia::render('onboarding');
})->name('onboarding');

Route::get('/workout-plan-chat', function () {
    return Inertia::render('workoutPlanChat');
})->name('workout-plan-chat');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
