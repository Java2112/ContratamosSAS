<?php

namespace App\Domains\Company\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\Application;
use App\Domains\Contracting\Models\ContractingProcess;
use App\Enums\Selection\ApplicationStatus;
use App\Enums\Contracting\ProcessStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CompanyReviewController extends Controller
{
    public function show(Application $application)
    {
        // Validar que la aplicación pertenezca a la empresa actual
        if ($application->vacancy->client_id !== auth()->user()->client?->id) {
            abort(403);
        }

        $application->load(['candidate', 'vacancy.client']);

        return Inertia::render('Company/CandidateReview/Show', [
            'application' => $application
        ]);
    }

    public function approve(Request $request, Application $application)
    {
        if ($application->vacancy->client_id !== auth()->user()->client?->id) {
            abort(403);
        }

        DB::transaction(function () use ($application) {
            // Actualizar estado de la aplicación
            $application->update([
                'status' => ApplicationStatus::APPROVED_BY_CLIENT
            ]);

            // Crear proceso de contratación
            ContractingProcess::create([
                'tenant_id' => $application->tenant_id,
                'application_id' => $application->id,
                'status' => ProcessStatus::PENDING_CONTRACTING,
                'agreed_salary' => $application->vacancy->min_salary, // O lo que se acuerde
                'cargo' => $application->vacancy->title,
            ]);
        });

        return redirect()->route('company.vacancies.show', $application->vacancy_id)
            ->with('success', 'Candidato aprobado correctamente. Se ha iniciado el proceso de contratación.');
    }

    public function reject(Request $request, Application $application)
    {
        if ($application->vacancy->client_id !== auth()->user()->client?->id) {
            abort(403);
        }

        $request->validate([
            'reason_rejection' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => ApplicationStatus::REJECTED_BY_CLIENT,
            'rejection_reason' => $request->reason_rejection
        ]);

        return redirect()->route('company.vacancies.show', $application->vacancy_id)
            ->with('success', 'Candidato rechazado.');
    }
}
