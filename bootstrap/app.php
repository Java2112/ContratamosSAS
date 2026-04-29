<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'company.auth' => \App\Http\Middleware\EnsureCompanyAuth::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(fn ($request) => $request->is('company/*') || $request->is('company') 
            ? route('company.login') 
            : route('login'));

        $middleware->redirectUsersTo(function ($request) {
            // Si el usuario es tipo empresa, redirigir a su dashboard de empresa
            if ($request->user() && $request->user()->role === \App\Enums\UserRole::EMPRESA) {
                return route('company.dashboard');
            }
            // De lo contrario, al dashboard administrativo/global
            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
