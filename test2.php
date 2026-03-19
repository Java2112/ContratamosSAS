<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$clients = \App\Domains\Commercial\Models\Client::withoutGlobalScopes()->get();
echo "Total clients: {$clients->count()}\n";
