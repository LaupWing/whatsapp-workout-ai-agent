<?php

namespace App\Enums;

enum WorkoutPlanGoal: string
{
    case STRENGTH = 'strength';
    case HYPERTROPHY = 'hypertrophy';
    case ENDURANCE = 'endurance';
    case WEIGHT_LOSS = 'weight_loss';
    case GENERAL_FITNESS = 'general_fitness';

    public function label(): string
    {
        return match ($this) {
            self::STRENGTH => 'Strength',
            self::HYPERTROPHY => 'Hypertrophy',
            self::ENDURANCE => 'Endurance',
            self::WEIGHT_LOSS => 'Weight Loss',
            self::GENERAL_FITNESS => 'General Fitness',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }
}
