<?php

namespace App\Enums\Selection;

enum ApplicationStatus: string
{
    case PRESELECTED = 'preseleccionado';
    case BACKGROUND_CHECK = 'antecedentes';
    case INTERVIEW = 'entrevista';
    case TESTS = 'pruebas';
    case SENT_TO_CLIENT = 'en_revision_empresa';
    case INTERVIEW_CLIENT = 'entrevista_cliente';
    case APPROVED_BY_CLIENT = 'aprobado_empresa';
    case REJECTED_BY_CLIENT = 'rechazado_empresa';
    case REJECTED_INTERNAL = 'rechazado_interno';
    case HIRED = 'contratado';
    case PENDING_CONTRACTING = 'pendiente_contratacion';
    case IN_CONTRACTING = 'en_contratacion';

    public function label(): string
    {
        return match($this) {
            self::PRESELECTED => 'Pre-seleccionado',
            self::BACKGROUND_CHECK => 'Revisión Antecedentes',
            self::INTERVIEW => 'En Entrevista',
            self::TESTS => 'En Pruebas',
            self::SENT_TO_CLIENT => 'En Revisión Empresa',
            self::INTERVIEW_CLIENT => 'Entrevista con Cliente',
            self::APPROVED_BY_CLIENT => 'Aprobado por Empresa',
            self::REJECTED_BY_CLIENT => 'Rechazado por Empresa',
            self::REJECTED_INTERNAL => 'Descartado Internamente',
            self::HIRED => 'Contratado',
            self::PENDING_CONTRACTING => 'Pendiente Contratación',
            self::IN_CONTRACTING => 'En Contratación',
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
            self::PENDING_CONTRACTING => 'cyan',
            self::IN_CONTRACTING => 'blue',
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
