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
            $q->whereIn('status', ['en_revision_empresa', 'aprobado_empresa', 'rechazado_empresa', 'entrevista_cliente'])
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
            'status' => 'required|in:aprobado_empresa,rechazado_empresa,entrevista_cliente',
            'feedback' => 'nullable|string'
        ]);

        $oldStatus = $application->status->value;

        // Ensure we only process if it was actually sent to client
        if (!in_array($oldStatus, ['en_revision_empresa', 'aprobado_empresa', 'rechazado_empresa', 'entrevista_cliente'])) {
            return back()->with('error', 'El candidato no está en un estado válido para revisión.');
        }

        $application->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'aprobado_empresa') {
            \App\Domains\Contracting\Models\ContractingProcess::create([
                'tenant_id' => $application->tenant_id,
                'application_id' => $application->id,
                'status' => \App\Enums\Contracting\ProcessStatus::PENDING_CONTRACTING,
                'agreed_salary' => $application->vacancy->min_salary,
                'cargo' => $application->vacancy->title,
            ]);
        }

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
