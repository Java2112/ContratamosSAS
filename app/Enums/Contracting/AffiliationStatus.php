<?php

namespace App\Enums\Contracting;

enum AffiliationStatus: string
{
    case PENDING = 'PENDIENTE';
    case ACTIVE = 'ACTIVA';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::ACTIVE => 'Activa',
        };
    }
}
