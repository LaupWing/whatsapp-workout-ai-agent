<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InspectUserData extends Command
{
    protected $signature = 'user:inspect {user_id}';
    protected $description = 'Inspect user data for testing';

    public function handle(): int
    {
        $user = User::with(['workouts.workoutExercises.exercise', 'aiInteractions'])
            ->find($this->argument('user_id'));

        if (!$user) {
            $this->error('User not found');
            return 1;
        }

        $this->info("═══════════════════════════════════════");
        $this->info("👤 USER: {$user->name}");
        $this->info("═══════════════════════════════════════");
        $this->newLine();

        $this->line("📱 WhatsApp: {$user->whatsapp_number}");
        $this->line("🎯 Goal: {$user->fitness_goal}");
        $this->line("📊 Level: {$user->experience_level}");
        $this->line("⚖️  Weight: {$user->current_weight_kg}kg → {$user->target_weight_kg}kg");
        $this->line("🔥 Streak: {$user->streak_days} days");
        $this->line("📅 Last Workout: " . ($user->last_workout_date?->format('Y-m-d') ?? 'Never'));
        $this->newLine();

        // Workouts
        $this->info("💪 WORKOUTS ({$user->workouts->count()})");
        $this->info("─────────────────────────────────────");

        foreach ($user->workouts->take(5) as $workout) {
            $this->line($workout->workout_date->format('Y-m-d') . " | {$workout->workout_type} | {$workout->total_sets} sets | {$workout->total_volume_kg}kg volume");

            foreach ($workout->workoutExercises->groupBy('exercise_id') as $exerciseGroup) {
                $exercise = $exerciseGroup->first()->exercise;
                $sets = $exerciseGroup->count();
                $avgReps = round($exerciseGroup->avg('reps'));
                $avgWeight = round($exerciseGroup->avg('weight_kg'), 1);

                $this->line("  • {$exercise->name}: {$sets}×{$avgReps} @ {$avgWeight}kg");
            }
            $this->newLine();
        }

        // AI Interactions
        $this->info("🤖 RECENT AI INTERACTIONS ({$user->aiInteractions->count()})");
        $this->info("─────────────────────────────────────");

        foreach ($user->aiInteractions->take(5) as $interaction) {
            $this->line($interaction->created_at->format('Y-m-d H:i') . " | {$interaction->agent_name}");
            $this->line("  In: " . Str::limit($interaction->user_input, 60));
            $this->line("  Out: " . Str::limit($interaction->agent_response, 60));
            $this->newLine();
        }

        return 0;
    }
}
