<?php

use App\Domains\Commercial\Actions\RegisterNewClientAction;
use Illuminate\Support\Facades\Config;

Config::set('queue.default', 'sync');

$action = app(RegisterNewClientAction::class);
$client = $action->execute([
    'document_type' => 'NIT',
    'document_number' => '900000000',
    'business_name' => 'Empresa de Pruebas',
    'contact_name' => 'Prueba Contacto',
    'email' => 'kuiner6@gmail.com',
    'phone' => '3506260666',
    'address' => 'Calle 123',
    'industry_sector' => 'Tecnologia'
]);

echo "Created client: " . $client->business_name . " with email: " . $client->email . "\n";
