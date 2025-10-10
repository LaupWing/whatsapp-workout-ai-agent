<?php

namespace App\Enums;

enum TrainingLocation: string
{
    case GYM = 'gym';
    case HOME_WITH_DUMBBELL = 'home_with_dumbbell';
    case BOTH = 'both';

    public function label(): string
    {
        return match ($this) {
            self::GYM => 'Gym',
            self::HOME_WITH_DUMBBELL => 'Home (Dumbbell required)',
            self::BOTH => 'Both',
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
