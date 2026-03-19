<?php

namespace App\Enums\Selection;

enum VacancyStatus: string
{
    case NEW = 'nueva';
    case ASSIGNED = 'asignada';
    case IN_PROGRESS = 'en_proceso';
    case IN_SEARCH = 'en_busqueda';
    case VALIDATION = 'en_validacion';
    case CLOSED = 'cerrada';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Nueva',
            self::ASSIGNED => 'Asignada',
            self::IN_PROGRESS => 'En Proceso',
            self::IN_SEARCH => 'En Búsqueda',
            self::VALIDATION => 'En Validación',
            self::CLOSED => 'Cerrada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'cyan',
            self::ASSIGNED => 'blue',
            self::IN_PROGRESS => 'orange',
            self::IN_SEARCH => 'indigo',
            self::VALIDATION => 'yellow',
            self::CLOSED => 'green',
            self::CANCELLED => 'red',
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
