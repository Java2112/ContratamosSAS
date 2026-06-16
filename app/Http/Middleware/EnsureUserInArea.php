<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserInArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $area): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role;
        $roleValue = is_object($userRole) ? $userRole->value : $userRole;

        // Global admin has access to all areas
        if ($roleValue === 'admin') {
            return $next($request);
        }

        // Validate if user role matches the allowed areas
        $allowedAreas = $this->getAreasByRole($roleValue);

        if (!in_array($area, $allowedAreas)) {
            abort(403, 'Acceso Denegado: Su rol no pertenece al área de ' . strtoupper($area) . '.');
        }

        return $next($request);
    }

    /**
     * Map roles to business areas.
     */
    private function getAreasByRole(string $role): array
    {
        return match ($role) {
            'coordinador' => ['comercial', 'seleccion'],
            'asistente', 'analista' => ['seleccion'],
            'jefe-contratacion' => ['contratacion'],
            'descargos' => ['descargos'],
            default => [],
        };
    }
}
