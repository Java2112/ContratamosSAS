<?php

$u = \App\Models\User::where('email', 'kuiner6@gmail.com')->first();
$u->password = \Illuminate\Support\Facades\Hash::make('Prueba2026*');
$u->must_change_password = true;
$u->save();
echo "OK Password reset to Prueba2026*\n";
