<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Domains\Commercial\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class FirstLoginController extends Controller
{
    /**
     * Show the first login view.
     */
    public function create()
    {
        return Inertia::render('Company/Auth/FirstLogin');
    }

    /**
     * Handle the incoming request to set the password.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'document_number' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // 1. Encontrar el usuario por email que sea EMPRESA
        $user = User::where('email', $request->email)
            ->where('role', \App\Enums\UserRole::EMPRESA)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros o no es una cuenta de empresa.',
            ]);
        }

        // 2. Revisar is_active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Su cuenta de empresa no se encuentra activa. Contacte al administrador.',
            ]);
        }

        // 3. Revisar si ya configuró la contraseña
        if (!$user->must_change_password) {
            throw ValidationException::withMessages([
                'email' => 'Esta cuenta ya ha configurado su contraseña. Proceda a iniciar sesión normalmente.',
            ]);
        }

        // 4. Encontrar el cliente (empresa) asociado para validar el NIT
        $client = Client::where('user_id', $user->id)
            ->where('document_number', $request->document_number)
            ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'document_number' => 'El NIT/Documento ingresado no coincide con los registros para este correo.',
            ]);
        }

        // 5. Todo es válido, actualizamos la contraseña e iniciamos sesión
        $user->forceFill([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ])->save();

        // Iniciar sesión automáticamente
        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('company.dashboard')->with('status', 'Contraseña configurada exitosamente e inicio de sesión correcto.');
    }
}
