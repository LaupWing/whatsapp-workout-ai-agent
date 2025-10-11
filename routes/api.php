<?php

use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserController;
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

Route::group(['prefix' => 'workouts'], function () {
    Route::post('/log', [WorkoutController::class, 'log']);
    Route::patch('/{workout}/exercises/{exercise}/latest', [WorkoutController::class, 'editLatestExercise']);
    Route::get('/history', [WorkoutController::class, 'history']);
    Route::get('/summary', [WorkoutController::class, 'summary']);
    Route::patch('/exercises/edit', [WorkoutController::class, 'editExercises']);
});

Route::group(['prefix' => 'exercises'], function () {
    Route::get('/', [ExerciseController::class, 'index']);
    Route::get('/search', [ExerciseController::class, 'search']);
    Route::get('/{id}', [ExerciseController::class, 'show']);
    Route::post('/', [ExerciseController::class, 'store']);
    Route::put('/{id}', [ExerciseController::class, 'update']);
    Route::delete('/{id}', [ExerciseController::class, 'destroy']);
});




// Web-based workout plan routes (session auth for Inertia frontend)
Route::middleware('web')->group(function () {
    Route::post('/workout-plans', [WorkoutPlanController::class, 'store'])->middleware('auth');
    Route::put('/workout-plans/{workoutPlan}/reorder', [WorkoutPlanController::class, 'reorder'])->middleware('auth');
});

// API-based workout plan routes (for ADK agent and external access)
Route::post('/workout-plans/generate', [WorkoutPlanController::class, 'generate']);
Route::get('/workout-plans/active', [WorkoutPlanController::class, 'getActivePlan']);
Route::get('/workout-plans/today', [WorkoutPlanController::class, 'getTodaysWorkout']);
