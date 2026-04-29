<?php

namespace App\Domains\Contracting\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Contracting\Models\ContractingProcess;
use App\Enums\Contracting\ProcessStatus;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ContractingDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $stats = [
            'total_active' => ContractingProcess::where('tenant_id', $tenantId)
                ->whereNotIn('status', [ProcessStatus::EMPLOYEE_ACTIVE, ProcessStatus::SENT_TO_PAYROLL])
                ->count(),
            'pending_new' => ContractingProcess::where('tenant_id', $tenantId)
                ->where('status', ProcessStatus::PENDING_CONTRACTING)
                ->count(),
            'pending_docs' => ContractingProcess::where('tenant_id', $tenantId)
                ->where('status', ProcessStatus::PENDING_DOCUMENTS)
                ->count(),
            'pending_medical' => ContractingProcess::where('tenant_id', $tenantId)
                ->where('status', ProcessStatus::PENDING_MEDICAL_EXAM)
                ->count(),
            'pending_contract' => ContractingProcess::where('tenant_id', $tenantId)
                ->where('status', ProcessStatus::PENDING_CONTRACT)
                ->count(),
        ];

        $recentProcesses = ContractingProcess::where('tenant_id', $tenantId)
            ->with(['application.candidate', 'application.vacancy'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Contracting/Dashboard', [
            'stats' => $stats,
            'processes' => $recentProcesses,
            'statuses' => ProcessStatus::toArray(),
        ]);
    }
}
