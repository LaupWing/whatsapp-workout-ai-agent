<?php

namespace App\Enums;

enum Equipment: string
{
    case BARBELL = 'barbell';
    case DUMBBELL = 'dumbbell';
    case MACHINE = 'machine';
    case BODYWEIGHT = 'bodyweight';
    case CABLE = 'cable';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BARBELL => 'Barbell',
            self::DUMBBELL => 'Dumbbell',
            self::MACHINE => 'Machine',
            self::BODYWEIGHT => 'Bodyweight',
            self::CABLE => 'Cable',
            self::OTHER => 'Other',
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
