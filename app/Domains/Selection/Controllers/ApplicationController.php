<?php

namespace App\Domains\Selection\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\Candidate;
use App\Domains\Selection\Models\Application;
use App\Domains\Selection\Models\ApplicationStage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $application->status->value ?? 'preseleccionado';
        
        $application->update([
            'status' => $request->status,
        ]);

        ApplicationStage::create([
            'application_id' => $application->id,
            'user_id' => $request->user()->id,
            'from_status' => $oldStatus,
            'to_status' => $request->status,
            'notes' => $request->notes,
        ]);

        if ($request->status === \App\Enums\Selection\ApplicationStatus::SENT_TO_CLIENT->value) {
            $application->load(['candidate', 'vacancy.client']);
            $pdf = Pdf::loadView('pdf.candidate', [
                'candidate' => $application->candidate,
                'vacancy' => $application->vacancy
            ]);
            
            // Save the report to public storage
            $tenantId = $application->tenant_id;
            $fileName = "informe_perfil_cand_{$application->candidate->id}_vac_{$application->vacancy->id}.pdf";
            $path = "tenant_{$tenantId}/reports/{$fileName}";
            
            Storage::disk('public')->put($path, $pdf->output());
        }

        return back()->with('success', 'Estado del candidato actualizado correctamente.');
    }
}
