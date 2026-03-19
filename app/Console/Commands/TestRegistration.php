<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the commercial client registration action';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $u = \App\Models\User::where('email', 'kuiner6@gmail.com')->first();
        if ($u) {
            $u->password = \Illuminate\Support\Facades\Hash::make('Prueba2026*');
            $u->must_change_password = true;
            $u->save();
            $this->info("OK Password reset to Prueba2026*");
        } else {
            $this->error("User not found");
        }
    }
}
