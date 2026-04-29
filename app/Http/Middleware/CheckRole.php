<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        
        // El rol en el enum es un string (e.g. 'admin', 'seleccion', etc.)
        // Convertimos el rol del usuario a string si es un enum case
        $roleValue = is_object($userRole) ? $userRole->value : $userRole;

        if (in_array($roleValue, $roles)) {
            return $next($request);
        }

        // Si es admin, tiene acceso a todo (opcional, dependiendo de la política)
        if ($roleValue === 'admin') {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a este módulo.');
    }
}
