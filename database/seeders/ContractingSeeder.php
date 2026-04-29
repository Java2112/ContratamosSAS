<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Contracting\Models\ContractingDocumentType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ContractingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Document Types
        $docs = [
            ['name' => 'Cédula de Ciudadanía', 'description' => 'Fotocopia de la cédula ampliada al 150%', 'is_required' => true],
            ['name' => 'Hoja de Vida', 'description' => 'CV actualizado con soportes', 'is_required' => true],
            ['name' => 'Certificados Laborales', 'description' => 'Últimas 2 certificaciones', 'is_required' => true],
            ['name' => 'Certificados Académicos', 'description' => 'Diploma o acta de grado', 'is_required' => true],
            ['name' => 'Certificado Bancario', 'description' => 'Para consignación de nómina', 'is_required' => true],
            ['name' => 'Foto 3x4', 'description' => 'Fondo blanco reciente', 'is_required' => false],
            ['name' => 'Certificado Antecedentes', 'description' => 'Procuraduría, Contraloría y Policía', 'is_required' => true],
        ];

        foreach ($docs as $doc) {
            // Use updateOrCreate to avoid duplicates if re-run
            \DB::table('contracting_document_types')->insert(array_merge($doc, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 2. Roles and Permissions
        $permissions = [
            'ver_candidatos_aprobados',
            'validar_documentos',
            'generar_contrato',
            'activar_afiliaciones',
            'enviar_nomina'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => \App\Enums\UserRole::JEFE_CONTRATACION->value, 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
    }
}
