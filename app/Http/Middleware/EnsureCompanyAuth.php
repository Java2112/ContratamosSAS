<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAuth
{
    /**
     * Verifica que el usuario esté autenticado Y tenga rol EMPRESA.
     * Si no está autenticado → redirige a company.login (no al login de empleados).
     * Si está autenticado pero no es empresa → aborta con 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // El redireccionamiento de usuarios NO autenticados ya se maneja globalmente en bootstrap/app.php
        if (! Auth::check()) {
            return redirect()->route('company.login');
        }

        // Si está autenticado pero no es del rol EMPRESA, cerramos sesión y mandamos al login de empresa
        if (Auth::user()->role !== UserRole::EMPRESA) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('company.login')
                ->with('error', 'Su cuenta no tiene permisos para acceder al Portal de Empresas.');
        }

        return $next($request);
    }
}
