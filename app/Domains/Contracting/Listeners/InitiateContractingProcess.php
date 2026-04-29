<?php

namespace App\Domains\Contracting\Listeners;

use App\Domains\Selection\Events\ApplicationStatusUpdated;
use App\Domains\Contracting\Models\ContractingProcess;
use App\Domains\Contracting\Models\ContractingDocumentType;
use App\Domains\Contracting\Models\EmployeeDocument;
use App\Enums\Selection\ApplicationStatus;
use App\Enums\Contracting\ProcessStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class InitiateContractingProcess implements ShouldQueue
{
    public function handle(ApplicationStatusUpdated $event): void
    {
        if ($event->newStatus !== ApplicationStatus::APPROVED_BY_CLIENT->value) {
            return;
        }

        DB::transaction(function () use ($event) {
            $application = $event->application;
            $application->load('vacancy');

            // 1. Create the process
            $process = ContractingProcess::create([
                'tenant_id' => $application->tenant_id,
                'application_id' => $application->id,
                'status' => ProcessStatus::PENDING_DOCUMENTS,
                'agreed_salary' => $application->vacancy->salary_max ?? 0, // Fallback or logic to get agreed salary
                'cargo' => $application->vacancy->title,
                'contract_type' => $application->vacancy->contract_type ?? 'Termino Indefinido',
            ]);

            // 2. Initialize required documents
            $documentTypes = ContractingDocumentType::where('is_active', true)->get();
            
            foreach ($documentTypes as $type) {
                EmployeeDocument::create([
                    'contracting_process_id' => $process->id,
                    'document_type_id' => $type->id,
                    'status' => \App\Enums\Contracting\DocumentStatus::PENDING,
                ]);
            }
        });
    }
}
