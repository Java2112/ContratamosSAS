<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domains\Selection\Events\ApplicationStatusUpdated;
use App\Domains\Contracting\Listeners\InitiateContractingProcess;
use App\Domains\Disciplinary\Models\DisciplinaryRecord;
use App\Policies\DisciplinaryRecordPolicy;
use Illuminate\Support\Facades\Gate;

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
        Gate::policy(DisciplinaryRecord::class, DisciplinaryRecordPolicy::class);

        // Register rate limiters for security hardening
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip())->response(function () {
                return response()->json([
                    'message' => 'Demasiados intentos de inicio de sesión. Por seguridad, su IP ha sido bloqueada temporalmente.'
                ], 429);
            });
        });

        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Vite::prefetch(concurrency: 3);

        Event::listen(
            ApplicationStatusUpdated::class,
            InitiateContractingProcess::class
        );
    }
}
