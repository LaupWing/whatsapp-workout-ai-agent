<?php

namespace App\Enums;

enum FitnessGoal: string
{
    case LOSE_WEIGHT = 'lose_weight';
    case BUILD_MUSCLE = 'build_muscle';
    case MAINTAIN = 'maintain';
    case STRENGTH = 'strength';
    case ENDURANCE = 'endurance';

    public function label(): string
    {
        return match ($this) {
            self::LOSE_WEIGHT => 'Lose Weight',
            self::BUILD_MUSCLE => 'Build Muscle',
            self::MAINTAIN => 'Maintain',
            self::STRENGTH => 'Strength',
            self::ENDURANCE => 'Endurance',
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
