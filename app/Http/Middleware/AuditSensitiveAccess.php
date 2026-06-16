<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditSensitiveAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $response = $next($request);

        // Audit only successful page reads (GET requests yielding 200 OK)
        if ($request->isMethod('GET') && $response->getStatusCode() === 200 && auth()->check()) {
            activity('auditoria_accesos')
                ->causedBy(auth()->user())
                ->withProperties([
                    'module' => $module,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('El usuario visualizó el módulo de ' . strtoupper($module));
        }

        return $response;
    }
}
