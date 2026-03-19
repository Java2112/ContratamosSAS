<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
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
        Role::create(['name' => 'commercial-analyst']);
        Role::create(['name' => 'recruiter']);
        Role::create(['name' => 'client']);
        
        // 3. Crear el usuario Administrador y asignarle el Tenant y Rol
        $adminUser = User::factory()->create([
            'name' => 'Admin Contratamos',
            'email' => 'admin@contratamos.com',
            'password' => bcrypt('password'), // cambiar en prod
            'tenant_id' => $tenant->id,
        ]);

        $adminUser->assignRole($adminRole);
    }
}
