<?php

namespace App\Enums;

enum SystemArea: string
{
    case GERENCIA = 'gerencia';
    case COMERCIAL = 'comercial';
    case SELECCION = 'seleccion';
    case CONTRATACION = 'contratacion';
    case NOMINA = 'nomina';
    case FINANCIERA = 'financiera';
    case EMPRESAS = 'empresas';

    public function label(): string
    {
        return match($this) {
            self::GERENCIA => 'Gerencia',
            self::COMERCIAL => 'Comercial',
            self::SELECCION => 'Selección',
            self::CONTRATACION => 'Contratación',
            self::NOMINA => 'Nómina',
            self::FINANCIERA => 'Financiera',
            self::EMPRESAS => 'Empresas',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }
}
