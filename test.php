<?php
$users = \App\Models\User::with('roles')->get();
echo "Total Users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo $user->name . " | " . $user->email . " | Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
}
