<?php

$u = \App\Models\User::where('email', 'kuiner6@gmail.com')->first();
echo "Role Value: " . $u->role->value . "\n";
echo "Enum match 1: " . ($u->role->value === 'empresa' ? 'YES' : 'NO') . "\n";
echo "Enum match 2: " . ($u->role === \App\Enums\UserRole::EMPRESA ? 'YES' : 'NO') . "\n";
echo "Dashboard Route: " . route('company.dashboard') . "\n";
