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
        // 1. Crear un Tenant (Sede Principal) de forma idempotente
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'principal.contratamos.com'],
            ['name' => 'Sede Principal - SISTEMA CONTRATAMOS']
        );

        // 2. Crear Roles
        $adminRole = Role::firstOrCreate(['name' => UserRole::ADMIN->value]);
        $coordinatorRole = Role::firstOrCreate(['name' => UserRole::COORDINADOR->value]);
        $analystRole = Role::firstOrCreate(['name' => UserRole::ANALISTA->value]);
        $assistantRole = Role::firstOrCreate(['name' => UserRole::ASISTENTE->value]);
        $clientRole = Role::firstOrCreate(['name' => UserRole::EMPRESA->value]);
        $contractingRole = Role::firstOrCreate(['name' => UserRole::JEFE_CONTRATACION->value]);
        $descargosRole = Role::firstOrCreate(['name' => UserRole::DESCARGOS->value]);
        $commAnalystRole = Role::firstOrCreate(['name' => 'commercial-analyst']);

        // Crear Permisos del Módulo de Descargos y asociar
        $permissions = [
            'crear_descargos',
            'editar_descargos',
            'finalizar_descargos',
            'generar_pdf_descargos',
            'ver_historial_descargos'
        ];

        foreach ($permissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName]);
            $descargosRole->givePermissionTo($permission);
            $adminRole->givePermissionTo($permission);
        }
        
        // 3. Crear Usuarios y asignarles el Tenant y Roles correspondientes
        
        // Admin
        $adminUser = User::where('email', 'admin@contratamos.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin Contratamos',
                'email' => 'admin@contratamos.com',
                'password' => bcrypt('Admin123*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::ADMIN,
                'system_area' => SystemArea::GERENCIA,
                'is_active' => true,
            ]);
            $adminUser->assignRole($adminRole);
        }

        // Gestor de Descargos
        $descargosUser = User::where('email', 'descargos@contratamos.com')->first();
        if (!$descargosUser) {
            $descargosUser = User::create([
                'name' => 'Gestor de Descargos',
                'email' => 'descargos@contratamos.com',
                'password' => bcrypt('Descargos123*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::DESCARGOS,
                'system_area' => SystemArea::DESCARGOS,
                'is_active' => true,
            ]);
            $descargosUser->assignRole($descargosRole);
        }

        // Coordinador Selección
        $coordinatorUser = User::where('email', 'pruebas@coordinador.com')->first();
        if (!$coordinatorUser) {
            $coordinatorUser = User::create([
                'name' => 'Coordinador Selección',
                'email' => 'pruebas@coordinador.com',
                'password' => bcrypt('Pruebas123*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::COORDINADOR,
                'system_area' => SystemArea::SELECCION,
                'is_active' => true,
            ]);
            $coordinatorUser->assignRole($coordinatorRole);
        }

        // Analista Selección
        $analystUser = User::where('email', 'prueba@candidato.com')->first();
        if (!$analystUser) {
            $analystUser = User::create([
                'name' => 'Analista Selección',
                'email' => 'prueba@candidato.com',
                'password' => bcrypt('Prueba10*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::ANALISTA,
                'system_area' => SystemArea::SELECCION,
                'is_active' => true,
            ]);
            $analystUser->assignRole($analystRole);
        }

        // Asistente Selección
        $assistantUser = User::where('email', 'prueba@asistente.com')->first();
        if (!$assistantUser) {
            $assistantUser = User::create([
                'name' => 'Asistente Selección',
                'email' => 'prueba@asistente.com',
                'password' => bcrypt('Asistente123*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::ASISTENTE,
                'system_area' => SystemArea::SELECCION,
                'is_active' => true,
            ]);
            $assistantUser->assignRole($assistantRole);
        }

        // Empresa Prueba
        $companyUser = User::where('email', 'kuiner6@gmail.com')->first();
        if (!$companyUser) {
            $companyUser = User::create([
                'name' => 'Empresa Prueba',
                'email' => 'kuiner6@gmail.com',
                'password' => bcrypt('Contraseña1*'),
                'tenant_id' => $tenant->id,
                'role' => UserRole::EMPRESA,
                'system_area' => SystemArea::EMPRESAS,
                'is_active' => true,
            ]);
            $companyUser->assignRole($clientRole);
        }

        // Crear el registro Client asociado al usuario empresa si no existe
        $client = Client::where('user_id', $companyUser->id)->first();
        if (!$client) {
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

        // 4. Crear Candidato, Vacante, Aplicación, Proceso de Contratación y Trabajador de Pruebas si no existen
        $candidate = \App\Domains\Selection\Models\Candidate::where('email', 'vargasjaviersanchez@gmail.com')->first();
        if (!$candidate) {
            $candidate = \App\Domains\Selection\Models\Candidate::create([
                'tenant_id' => $tenant->id,
                'document_type' => 'CC',
                'document_number' => '1010211001',
                'first_name' => 'Javier (Prueba)',
                'last_name' => 'Vargas',
                'email' => 'vargasjaviersanchez@gmail.com',
                'phone' => '3506260666',
                'cv_path' => null,
                'source' => 'Oficina',
                'is_active' => true,
            ]);
        }

        $clientRecord = Client::where('user_id', $companyUser->id)->first();
        if ($clientRecord) {
            $vacancy = \App\Domains\Selection\Models\Vacancy::where('client_id', $clientRecord->id)->first();
            if (!$vacancy) {
                $vacancy = \App\Domains\Selection\Models\Vacancy::create([
                    'tenant_id' => $tenant->id,
                    'client_id' => $clientRecord->id,
                    'title' => 'Operario de Producción',
                    'description' => 'Vacante de prueba para operario de producción',
                    'positions_required' => 1,
                    'status' => \App\Enums\Selection\VacancyStatus::NEW->value,
                    'priority' => \App\Enums\Selection\VacancyPriority::NORMAL->value,
                    'coordinator_id' => null,
                    'analyst_id' => null,
                ]);
            }

            $application = \App\Domains\Selection\Models\Application::where('vacancy_id', $vacancy->id)
                ->where('candidate_id', $candidate->id)
                ->first();
            if (!$application) {
                $application = \App\Domains\Selection\Models\Application::create([
                    'tenant_id' => $tenant->id,
                    'vacancy_id' => $vacancy->id,
                    'candidate_id' => $candidate->id,
                    'status' => \App\Enums\Selection\ApplicationStatus::PRESELECTED->value,
                    'is_active' => true,
                ]);
            }

            $process = \App\Domains\Contracting\Models\ContractingProcess::where('application_id', $application->id)->first();
            if (!$process) {
                $process = \App\Domains\Contracting\Models\ContractingProcess::create([
                    'tenant_id' => $tenant->id,
                    'application_id' => $application->id,
                    'status' => \App\Enums\Contracting\ProcessStatus::PENDING_DOCUMENTS->value,
                    'agreed_salary' => 1400000,
                    'contract_type' => 'Término Indefinido',
                    'cargo' => 'Operario de Producción',
                    'started_at' => now(),
                ]);
            }

            $employee = \App\Domains\Contracting\Models\Employee::where('contracting_process_id', $process->id)->first();
            if (!$employee) {
                \App\Domains\Contracting\Models\Employee::create([
                    'tenant_id' => $tenant->id,
                    'contracting_process_id' => $process->id,
                    'document_type' => 'CC',
                    'document_number' => '1010211001',
                    'first_name' => 'Javier (Prueba)',
                    'last_name' => 'Vargas',
                    'email' => 'vargasjaviersanchez@gmail.com',
                    'phone' => '3506260666',
                    'cargo' => 'Operario de Producción',
                    'salary' => 1400000,
                    'hired_at' => now(),
                    'is_active' => true,
                ]);
            }
        }
    }
}
