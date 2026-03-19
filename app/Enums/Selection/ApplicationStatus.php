<?php

namespace App\Enums\Selection;

enum ApplicationStatus: string
{
    case PRESELECTED = 'preseleccionado';
    case BACKGROUND_CHECK = 'antecedentes';
    case INTERVIEW = 'entrevista';
    case TESTS = 'pruebas';
    case SENT_TO_CLIENT = 'enviado_cliente';
    case INTERVIEW_CLIENT = 'entrevista_cliente';
    case APPROVED_BY_CLIENT = 'aprobado_cliente';
    case REJECTED_BY_CLIENT = 'rechazado_cliente';
    case REJECTED_INTERNAL = 'rechazado_interno';
    case HIRED = 'contratado';

    public function label(): string
    {
        return match($this) {
            self::PRESELECTED => 'Pre-seleccionado',
            self::BACKGROUND_CHECK => 'Revisión Antecedentes',
            self::INTERVIEW => 'En Entrevista',
            self::TESTS => 'En Pruebas',
            self::SENT_TO_CLIENT => 'Enviado al Cliente',
            self::INTERVIEW_CLIENT => 'Entrevista con Cliente',
            self::APPROVED_BY_CLIENT => 'Aprobado por Cliente',
            self::REJECTED_BY_CLIENT => 'Rechazado por Cliente',
            self::REJECTED_INTERNAL => 'Descartado Internamente',
            self::HIRED => 'Seleccionado / Contratado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PRESELECTED => 'gray',
            self::BACKGROUND_CHECK => 'orange',
            self::INTERVIEW => 'blue',
            self::TESTS => 'indigo',
            self::SENT_TO_CLIENT => 'yellow',
            self::INTERVIEW_CLIENT => 'purple',
            self::APPROVED_BY_CLIENT => 'green',
            self::REJECTED_BY_CLIENT => 'red',
            self::REJECTED_INTERNAL => 'red',
            self::HIRED => 'emerald',
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
