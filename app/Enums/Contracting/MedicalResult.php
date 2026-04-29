<?php

namespace App\Enums\Contracting;

enum MedicalResult: string
{
    case FIT = 'APTO';
    case UNFIT = 'NO_APTO';
    case FIT_WITH_RESTRICTIONS = 'APTO_CON_RESTRICCIONES';

    public function label(): string
    {
        return match($this) {
            self::FIT => 'Apto',
            self::UNFIT => 'No Apto',
            self::FIT_WITH_RESTRICTIONS => 'Apto con Restricciones',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::FIT => 'green',
            self::UNFIT => 'red',
            self::FIT_WITH_RESTRICTIONS => 'orange',
        };
    }
}
