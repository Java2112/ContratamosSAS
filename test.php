<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\User::first();
auth()->login($admin);

try {
    $action = new \App\Domains\Commercial\Actions\RegisterNewClientAction();
    $data = [
        'email' => 'test@test.com',
        'business_name' => 'test',
        'document_type' => 'NIT',
        'document_number' => '12345',
        'contact_name' => 'Test',
        'phone' => '123',
        'address' => 'Test',
        'industry_sector' => 'IT'
    ];
    $action->execute($data);
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    echo $e->getTraceAsString();
}
