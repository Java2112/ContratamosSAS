<?php

namespace App\Enums\Contracting;

enum ContractStatus: string
{
    case GENERATED = 'GENERADO';
    case SENT_FOR_SIGNATURE = 'ENVIADO_FIRMA';
    case SIGNED = 'FIRMADO';
    case ACTIVE = 'ACTIVO';

    public function label(): string
    {
        return match($this) {
            self::GENERATED => 'Generado',
            self::SENT_FOR_SIGNATURE => 'Enviado para Firma',
            self::SIGNED => 'Firmado',
            self::ACTIVE => 'Activo',
        };
    }
}
