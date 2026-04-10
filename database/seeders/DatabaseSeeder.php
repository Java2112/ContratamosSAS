<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Enums\UserRole;
use App\Enums\SystemArea;
use App\Domains\Commercial\Models\Client;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear un Tenant (Sede Principal)
        $tenant = Tenant::create([
            'name' => 'Sede Principal - SISTEMA CONTRATAMOS',
            'domain' => 'principal.contratamos.com'
        ]);

        // 2. Crear Roles
        $adminRole = Role::create(['name' => 'super-admin']);
        $coordinatorRole = Role::create(['name' => 'selection-coordinator']);
        $analystRole = Role::create(['name' => 'recruiter']); // Analista Selección
        $assistantRole = Role::create(['name' => 'selection-assistant']); // Asistente Selección
        $clientRole = Role::create(['name' => 'client']);
        Role::create(['name' => 'commercial-analyst']);
        
        // 3. Crear Usuarios y asignarles el Tenant y Roles correspondientes
        
        // Admin
        $adminUser = User::factory()->create([
            'name' => 'Admin Contratamos',
            'email' => 'admin@contratamos.com',
            'password' => bcrypt('Admin123*'),
            'tenant_id' => $tenant->id,
            'role' => UserRole::ADMIN,
            'system_area' => SystemArea::GERENCIA,
        ]);
        $adminUser->assignRole($adminRole);

        // Coordinador Selección
        $coordinatorUser = User::factory()->create([
            'name' => 'Coordinador Selección',
            'email' => 'pruebas@coordinador.com',
            'password' => bcrypt('Pruebas123*'),
            'tenant_id' => $tenant->id,
            'role' => UserRole::COORDINADOR,
            'system_area' => SystemArea::SELECCION,
        ]);
        $coordinatorUser->assignRole($coordinatorRole);

        // Analista Selección
        $analystUser = User::factory()->create([
            'name' => 'Analista Selección',
            'email' => 'prueba@candidato.com',
            'password' => bcrypt('Prueba10*'),
            'tenant_id' => $tenant->id,
            'role' => UserRole::ANALISTA,
            'system_area' => SystemArea::SELECCION,
        ]);
        $analystUser->assignRole($analystRole);

        // Asistente Selección
        $assistantUser = User::factory()->create([
            'name' => 'Asistente Selección',
            'email' => 'prueba@asistente.com',
            'password' => bcrypt('Asistente123*'),
            'tenant_id' => $tenant->id,
            'role' => UserRole::ASISTENTE,
            'system_area' => SystemArea::SELECCION,
        ]);
        $assistantUser->assignRole($assistantRole);

        // Empresa Prueba
        $companyUser = User::factory()->create([
            'name' => 'Empresa Prueba',
            'email' => 'kuiner6@gmail.com',
            'password' => bcrypt('Contraseña1*'),
            'tenant_id' => $tenant->id,
            'role' => UserRole::EMPRESA,
            'system_area' => SystemArea::EMPRESAS,
        ]);
        $companyUser->assignRole($clientRole);

        // Crear el registro Client asociado al usuario empresa
        Client::create([
            'tenant_id' => $tenant->id,
            'user_id' => $companyUser->id,
            'document_type' => 'NIT',
            'document_number' => '900123456-7',
            'business_name' => 'Empresa Prueba S.A.S',
            'contact_name' => 'Empresa Prueba',
            'email' => 'kuiner6@gmail.com',
            'phone' => '3001234567',
            'address' => 'Calle 100 #10-20, Bogotá',
            'industry_sector' => 'Tecnología',
            'status' => 'active',
        ]);
    }
}
