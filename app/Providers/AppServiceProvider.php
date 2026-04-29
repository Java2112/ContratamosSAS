<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domains\Selection\Events\ApplicationStatusUpdated;
use App\Domains\Contracting\Listeners\InitiateContractingProcess;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(
            ApplicationStatusUpdated::class,
            InitiateContractingProcess::class
        );
    }
}
