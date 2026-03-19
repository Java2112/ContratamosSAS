<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Enums\UserRole;

class TestLoginRedirect extends Command
{
    protected $signature = 'app:test-login';
    protected $description = 'Test login logic';

    public function handle()
    {
        $u = User::where('email', 'kuiner6@gmail.com')->first();
        if (!$u) {
            $this->error('User not found');
            return;
        }

        $this->info("User Role Value: " . $u->role->value);
        $this->info("Is Empresa? " . ($u->role === UserRole::EMPRESA ? 'YES' : 'NO'));
        
        if ($u->role->value === 'empresa' || $u->role === UserRole::EMPRESA) {
            $this->info("Login Redirect: " . route('company.dashboard', absolute: false));
        } else {
            $this->info("Login Redirect: " . route('dashboard', absolute: false));
        }
        
        $this->info("Must change password? " . ($u->must_change_password ? 'YES' : 'NO'));
    }
}
