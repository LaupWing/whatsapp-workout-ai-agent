<?php

namespace App\Enums;

enum ExerciseCategory: string
{
    case STRENGTH = 'strength';
    case CARDIO = 'cardio';
    case FLEXIBILITY = 'flexibility';
    case SPORTS = 'sports';

    public function label(): string
    {
        return match ($this) {
            self::STRENGTH => 'Strength',
            self::CARDIO => 'Cardio',
            self::FLEXIBILITY => 'Flexibility',
            self::SPORTS => 'Sports',
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
