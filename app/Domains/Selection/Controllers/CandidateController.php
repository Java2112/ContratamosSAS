<?php

namespace App\Domains\Selection\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Selection\Models\Vacancy;
use App\Domains\Selection\Models\Candidate;
use App\Domains\Selection\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function storeForVacancy(Request $request, Vacancy $vacancy)
    {
        $request->validate([
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:50',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:255',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        $tenant_id = $request->user()->tenant_id;

        // Check if candidate already exists
        $candidate = Candidate::where('tenant_id', $tenant_id)
            ->where('document_type', $request->document_type)
            ->where('document_number', $request->document_number)
            ->first();

        // Handle File Upload
        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $cvPath = $file->storeAs("tenant_{$tenant_id}/cvs", $filename, 'public');
        }

        if (!$candidate) {
            $candidate = Candidate::create([
                'tenant_id' => $tenant_id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'source' => $request->source,
                'cv_path' => $cvPath ? '/storage/' . $cvPath : null,
            ]);
        } else {
            // Update CV if provided
            if ($cvPath) {
                $candidate->update(['cv_path' => '/storage/' . $cvPath]);
            }
        }

        // Attach to vacancy safely
        $application = Application::firstOrCreate([
            'tenant_id' => $tenant_id,
            'vacancy_id' => $vacancy->id,
            'candidate_id' => $candidate->id,
        ]);

        return back()->with('success', 'Candidato agregado correctamente a la vacante.');
    }
}
