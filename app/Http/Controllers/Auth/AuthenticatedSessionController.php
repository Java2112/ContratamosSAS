<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $role = $user->role; // Cast to UserRole enum (or null)

        // Bloqueo de seguridad: las empresas no pueden ingresar por el portal de empleados
        if ($role === UserRole::EMPRESA) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Este portal es exclusivo para empleados de Contratamos. Las empresas deben ingresar a través del Portal de Clientes.',
            ]);
        }

        // Coordinador, Analista, Asistente → Dashboard de Selección
        if (in_array($role, [UserRole::COORDINADOR, UserRole::ANALISTA, UserRole::ASISTENTE])) {
            return redirect()->intended(route('selection.dashboard', absolute: false));
        }

        // Admin y demás → Dashboard administrativo
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
