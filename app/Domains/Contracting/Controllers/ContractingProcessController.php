<?php

namespace App\Domains\Contracting\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Contracting\Models\ContractingProcess;
use App\Domains\Contracting\Models\EmployeeDocument;
use App\Domains\Contracting\Models\MedicalExam;
use App\Domains\Contracting\Models\EmploymentContract;
use App\Domains\Contracting\Models\EmployeeAffiliation;
use App\Domains\Contracting\Models\Employee;
use App\Enums\Contracting\ProcessStatus;
use App\Enums\Contracting\DocumentStatus;
use App\Enums\Contracting\MedicalResult;
use App\Enums\Contracting\ContractStatus;
use App\Enums\Contracting\AffiliationStatus;
use App\Enums\Selection\ApplicationStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ContractingProcessController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        
        $query = ContractingProcess::where('tenant_id', $tenantId)
            ->with(['application.candidate', 'application.vacancy.client']);

        if ($request->search) {
            $query->whereHas('application.candidate', function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $processes = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Contracting/Process/Index', [
            'processes' => $processes,
            'filters' => $request->only(['search', 'status']),
            'statuses' => ProcessStatus::toArray()
        ]);
    }

    public function show(ContractingProcess $process)
    {
        $process->load([
            'application.candidate',
            'application.vacancy.client',
            'documents.documentType',
            'medicalExam',
            'contract',
            'affiliations'
        ]);

        return Inertia::render('Contracting/Process/Show', [
            'process' => $process,
            'enums' => [
                'doc_statuses' => [
                    ['value' => DocumentStatus::PENDING->value, 'label' => DocumentStatus::PENDING->label()],
                    ['value' => DocumentStatus::UPLOADED->value, 'label' => DocumentStatus::UPLOADED->label()],
                    ['value' => DocumentStatus::APPROVED->value, 'label' => DocumentStatus::APPROVED->label()],
                    ['value' => DocumentStatus::REJECTED->value, 'label' => DocumentStatus::REJECTED->label()],
                ],
                'medical_results' => [
                    ['value' => MedicalResult::FIT->value, 'label' => MedicalResult::FIT->label()],
                    ['value' => MedicalResult::UNFIT->value, 'label' => MedicalResult::UNFIT->label()],
                    ['value' => MedicalResult::FIT_WITH_RESTRICTIONS->value, 'label' => MedicalResult::FIT_WITH_RESTRICTIONS->label()],
                ]
            ]
        ]);
    }

    public function validateDocument(Request $request, EmployeeDocument $document)
    {
        $request->validate([
            'status' => 'required|string',
            'rejection_reason' => 'nullable|string'
        ]);

        $document->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'validated_at' => now(),
            'validated_by' => $request->user()->id,
        ]);

        // Check if all required documents are approved
        $process = $document->process;
        $totalRequired = $process->documents()->whereHas('documentType', fn($q) => $q->where('is_required', true))->count();
        $approvedRequired = $process->documents()->whereHas('documentType', fn($q) => $q->where('is_required', true))
            ->where('status', DocumentStatus::APPROVED->value)
            ->count();

        if ($totalRequired > 0 && $totalRequired === $approvedRequired) {
            $process->update(['status' => ProcessStatus::PENDING_MEDICAL_EXAM]);
        }

        return back()->with('success', 'Documento validado correctamente.');
    }

    public function recordMedicalExam(Request $request, ContractingProcess $process)
    {
        $request->validate([
            'provider_name' => 'required|string',
            'scheduled_date' => 'required|date',
            'result' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $exam = MedicalExam::updateOrCreate(
            ['contracting_process_id' => $process->id],
            $request->only(['provider_name', 'scheduled_date', 'result', 'observations'])
        );

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("contracting/{$process->id}/medical", 'public');
            $exam->update(['file_path' => $path]);
        }

        if ($request->result === MedicalResult::FIT->value || $request->result === MedicalResult::FIT_WITH_RESTRICTIONS->value) {
            $process->update(['status' => ProcessStatus::PENDING_CONTRACT]);
        } elseif ($request->result === MedicalResult::UNFIT->value) {
            $process->update(['status' => ProcessStatus::RETURNED_TO_SELECTION]);
            $process->application->update(['status' => ApplicationStatus::REJECTED_INTERNAL->value]);
        }

        return back()->with('success', 'Examen médico registrado correctamente.');
    }

    public function uploadContract(Request $request, ContractingProcess $process)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date'
        ]);

        $path = $request->file('file')->store("contracting/{$process->id}/contract", 'public');

        EmploymentContract::updateOrCreate(
            ['contracting_process_id' => $process->id],
            [
                'file_path' => $path,
                'status' => ContractStatus::GENERATED,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]
        );

        return back()->with('success', 'Contrato cargado correctamente.');
    }

    public function signContract(Request $request, ContractingProcess $process)
    {
        $contract = $process->contract;
        if (!$contract) return back()->with('error', 'No hay contrato para firmar.');

        $contract->update([
            'status' => ContractStatus::SIGNED,
            'signed_at' => now(),
            'signed_by' => $request->user()->id,
        ]);

        $process->update(['status' => ProcessStatus::PENDING_AFFILIATIONS]);

        return back()->with('success', 'Contrato firmado digitalmente.');
    }

    public function saveAffiliation(Request $request, ContractingProcess $process)
    {
        $request->validate([
            'type' => 'required|string',
            'entity_name' => 'required|string',
            'affiliation_number' => 'nullable|string',
            'affiliation_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        EmployeeAffiliation::updateOrCreate(
            [
                'contracting_process_id' => $process->id,
                'type' => $request->type
            ],
            $request->only(['entity_name', 'affiliation_number', 'affiliation_date', 'status'])
        );

        // Check if all mandatory affiliations are active (EPS, ARL, Pensión, Cesantías)
        $requiredTypes = ['EPS', 'ARL', 'PENSIÓN', 'CESANTÍAS'];
        $activeAffiliations = $process->affiliations()->whereIn('type', $requiredTypes)
            ->where('status', AffiliationStatus::ACTIVE->value)
            ->count();

        if ($activeAffiliations >= 4) {
            $process->update(['status' => ProcessStatus::EMPLOYEE_ACTIVE]);
            $this->createEmployeeRecord($process);
        }

        return back()->with('success', 'Afiliación registrada.');
    }

    private function createEmployeeRecord(ContractingProcess $process)
    {
        $application = $process->application;
        $candidate = $application->candidate;

        Employee::create([
            'tenant_id' => $process->tenant_id,
            'contracting_process_id' => $process->id,
            'document_type' => $candidate->document_type ?? 'CC',
            'document_number' => $candidate->document_number ?? '',
            'first_name' => $candidate->first_name,
            'last_name' => $candidate->last_name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'cargo' => $process->cargo,
            'salary' => $process->agreed_salary,
            'hired_at' => now(),
            'is_active' => true,
        ]);
        
        $application->update(['status' => ApplicationStatus::HIRED->value]);
    }

    public function sendToPayroll(ContractingProcess $process)
    {
        $process->update(['status' => ProcessStatus::SENT_TO_PAYROLL]);
        return back()->with('success', 'Empleado enviado al área de Nómina.');
    }
}
