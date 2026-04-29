<?php

namespace App\Enums\Contracting;

enum ProcessStatus: string
{
    case PENDING_DOCUMENTS = 'PENDIENTE_DOCUMENTOS';
    case DOCUMENTS_COMPLETED = 'DOCUMENTOS_COMPLETOS';
    case PENDING_MEDICAL_EXAM = 'PENDIENTE_EXAMEN_MEDICO';
    case MEDICAL_EXAM_APPROVED = 'EXAMEN_APROBADO';
    case PENDING_CONTRACT = 'PENDIENTE_CONTRATO';
    case CONTRACT_GENERATED = 'CONTRATO_GENERADO';
    case CONTRACT_SIGNED = 'CONTRATO_FIRMADO';
    case PENDING_AFFILIATIONS = 'PENDIENTE_AFILIACIONES';
    case EMPLOYEE_ACTIVE = 'EMPLEADO_ACTIVO';
    case SENT_TO_PAYROLL = 'ENVIADO_A_NOMINA';
    case REJECTED_MEDICAL = 'REJECTED_MEDICAL';
    case RETURNED_TO_SELECTION = 'DEVUELTO_A_SELECCION';
    case PENDING_CONTRACTING = 'pendiente_contratacion';

    public function label(): string
    {
        return match($this) {
            self::PENDING_DOCUMENTS => 'Pendiente Documentos',
            self::DOCUMENTS_COMPLETED => 'Documentos Completos',
            self::PENDING_MEDICAL_EXAM => 'Pendiente Examen Médico',
            self::MEDICAL_EXAM_APPROVED => 'Examen Médico Aprobado',
            self::PENDING_CONTRACT => 'Pendiente Contrato',
            self::CONTRACT_GENERATED => 'Contrato Generado',
            self::CONTRACT_SIGNED => 'Contrato Firmado',
            self::PENDING_AFFILIATIONS => 'Pendiente Afiliaciones',
            self::EMPLOYEE_ACTIVE => 'Empleado Activo',
            self::SENT_TO_PAYROLL => 'Enviado a Nómina',
            self::REJECTED_MEDICAL => 'Rechazado Médico',
            self::RETURNED_TO_SELECTION => 'Devuelto a Selección',
            self::PENDING_CONTRACTING => 'Pendiente Contratación',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING_DOCUMENTS => 'orange',
            self::DOCUMENTS_COMPLETED => 'blue',
            self::PENDING_MEDICAL_EXAM => 'indigo',
            self::MEDICAL_EXAM_APPROVED => 'green',
            self::PENDING_CONTRACT => 'yellow',
            self::CONTRACT_GENERATED => 'amber',
            self::CONTRACT_SIGNED => 'emerald',
            self::PENDING_AFFILIATIONS => 'purple',
            self::EMPLOYEE_ACTIVE => 'green',
            self::SENT_TO_PAYROLL => 'slate',
            self::REJECTED_MEDICAL => 'red',
            self::RETURNED_TO_SELECTION => 'red',
            self::PENDING_CONTRACTING => 'cyan',
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
