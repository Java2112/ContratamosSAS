<?php

namespace App\Domains\Selection\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\MagicLink;
use App\Domains\Selection\Models\Application;
use App\Domains\Selection\Models\ApplicationStage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MagicLinkController extends Controller
{
    public function show($token)
    {
        $magicLink = MagicLink::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $vacancy = $magicLink->vacancy()->with(['client', 'applications' => function($q) {
            $q->whereIn('status', ['enviado_cliente', 'aprobado_cliente', 'rechazado_cliente', 'entrevista_cliente'])
              ->with('candidate');
        }])->first();

        return Inertia::render('Selection/MagicLink/Review', [
            'vacancy' => $vacancy,
            'token' => $token
        ]);
    }

    public function updateApplicationStatus(Request $request, $token, Application $application)
    {
        $magicLink = MagicLink::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();
        
        if ($application->vacancy_id !== $magicLink->vacancy_id) {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'status' => 'required|in:aprobado_cliente,rechazado_cliente,entrevista_cliente',
            'feedback' => 'nullable|string'
        ]);

        $oldStatus = $application->status->value;

        // Ensure we only process if it was actually sent to client
        if (!in_array($oldStatus, ['enviado_cliente', 'aprobado_cliente', 'rechazado_cliente', 'entrevista_cliente'])) {
            return back()->with('error', 'El candidato no está en un estado válido para revisión.');
        }

        $application->update([
            'status' => $validated['status'],
        ]);

        ApplicationStage::create([
            'application_id' => $application->id,
            'user_id' => null, // Null indicates external action
            'from_status' => $oldStatus,
            'to_status' => $validated['status'],
            'notes' => 'Respuesta del Cliente: ' . ($validated['feedback'] ?? 'Sin comentarios adicionales.'),
        ]);

        return back()->with('success', 'Tu respuesta ha sido registrada. ¡Gracias!');
    }
}
