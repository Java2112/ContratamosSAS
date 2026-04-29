<?php

namespace App\Enums\Contracting;

enum DocumentStatus: string
{
    case PENDING = 'PENDIENTE';
    case UPLOADED = 'CARGADO';
    case APPROVED = 'APROBADO';
    case REJECTED = 'RECHAZADO';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::UPLOADED => 'Cargado',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::UPLOADED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }
}
