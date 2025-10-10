<?php

namespace App\Enums;

enum MuscleGroup: string
{
    case CHEST = 'chest';
    case LEGS = 'legs';
    case BACK = 'back';
    case SHOULDERS = 'shoulders';
    case ARMS = 'arms';
    case CORE = 'core';
    case FULL_BODY = 'full_body';

    public function label(): string
    {
        return match ($this) {
            self::CHEST => 'Chest',
            self::LEGS => 'Legs',
            self::BACK => 'Back',
            self::SHOULDERS => 'Shoulders',
            self::ARMS => 'Arms',
            self::CORE => 'Core',
            self::FULL_BODY => 'Full Body',
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
