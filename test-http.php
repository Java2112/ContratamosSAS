<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

$user = User::where('email', 'kuiner6@gmail.com')->first();

// Simulate a request acting as the user
$request = Request::create('/company/dashboard', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Redirect: " . $response->headers->get('Location') . "\n";
