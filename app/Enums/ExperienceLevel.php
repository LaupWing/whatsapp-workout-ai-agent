<?php

namespace App\Enums;

enum ExperienceLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => 'Beginner',
            self::INTERMEDIATE => 'Intermediate',
            self::ADVANCED => 'Advanced',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BEGINNER => '0–1 year',
            self::INTERMEDIATE => '1–3 years',
            self::ADVANCED => '3+ years',
        };
    }

    public function fullDescription(): string
    {
        return match ($this) {
            self::BEGINNER => 'Just starting or returning after a long break',
            self::INTERMEDIATE => 'Consistent training experience, understands basic form',
            self::ADVANCED => 'Regular training, focused goals, solid technique',
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
                'description' => $case->description(),
                'fullDescription' => $case->fullDescription(),
            ],
            self::cases()
        );
    }
}
