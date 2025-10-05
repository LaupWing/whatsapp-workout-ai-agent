<?php

use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// WhatsApp webhook endpoints
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'webhook']);

Route::post('/workouts/log', [WorkoutController::class, 'log']);
Route::patch('/workouts/{workout}/exercises/{exercise}/latest', [WorkoutController::class, 'editLatestExercise']);
Route::get('/workouts/history', [WorkoutController::class, 'history']);
Route::get('/workouts/summary', [WorkoutController::class, 'summary']);
// Route::get('/exercises/search', [ExerciseController::class, 'search']);


Route::get('/workout-plans/active', [WorkoutPlanController::class, 'getActivePlan']);
Route::get('/workout-plans/today', [WorkoutPlanController::class, 'getTodaysWorkout']);
