<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case COORDINADOR = 'coordinador';
    case ASISTENTE = 'asistente';
    case ANALISTA = 'analista';
    case EMPRESA = 'empresa';
    case JEFE_CONTRATACION = 'jefe-contratacion';
    case DESCARGOS = 'descargos';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador Global',
            self::COORDINADOR => 'Coordinador',
            self::ASISTENTE => 'Asistente',
            self::ANALISTA => 'Analista / Operativo',
            self::EMPRESA => 'Empresa Cliente',
            self::JEFE_CONTRATACION => 'Jefe de Contratación',
            self::DESCARGOS => 'Gestor de Descargos',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
