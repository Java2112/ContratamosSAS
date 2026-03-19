<?php

namespace App\Domains\Commercial\Actions;

use App\Models\User;
use App\Domains\Commercial\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterNewClientAction
{
    /**
     * Registra el cliente y crea el usuario base para el Portal.
     * 
     * @param array $data Los datos validados del request.
     * @return Client
     */
    public function execute(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear usuario base de portal con contraseña aleatoria inútil inicialmente
            // La verdadera se establecerá en el primer ingreso con NIT y Correo
            $rawPassword = Str::random(32);
            
            $user = User::create([
                'name' => 'Admin ' . $data['business_name'],
                'email' => $data['email'],
                'password' => Hash::make($rawPassword),
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'role' => \App\Enums\UserRole::EMPRESA,
                'system_area' => \App\Enums\SystemArea::EMPRESAS,
                'must_change_password' => true,
            ]);
            
            // Asignar rol de cliente empresarial
            $user->assignRole('client');

            // 2. Crear registro del cliente
            $client = Client::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'user_id' => $user->id,
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'business_name' => $data['business_name'],
                'contact_name' => $data['contact_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'industry_sector' => $data['industry_sector'],
            ]);

            // 3. Despachar el Job para enviar correo y whatsapp de bienvenida
            \App\Domains\Commercial\Jobs\SendClientWelcomeJob::dispatch($client);

            return $client;
        });
    }
}
