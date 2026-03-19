<?php

namespace App\Enums\Selection;

enum VacancyPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Baja',
            self::NORMAL => 'Normal',
            self::URGENT => 'Urgente',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'gray',
            self::NORMAL => 'blue',
            self::URGENT => 'red',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'color' => $case->color()
        ], self::cases());
    }
}
