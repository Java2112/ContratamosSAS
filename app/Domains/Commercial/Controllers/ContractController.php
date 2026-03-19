<?php

namespace App\Domains\Commercial\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Commercial\Models\Client;
use App\Domains\Commercial\Models\ContractService;
use App\Domains\Commercial\Actions\CreateContractAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{
    /**
     * Display a listing of the contracts.
     */
    public function index()
    {
        $contracts = ContractService::with('client')->latest()->get();

        return Inertia::render('Commercial/Contracts/Index', [
            'contracts' => $contracts
        ]);
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        $clients = Client::select('id', 'business_name', 'document_type', 'document_number')->get();

        return Inertia::render('Commercial/Contracts/Create', [
            'clients' => $clients
        ]);
    }

    /**
     * Store a newly created contract in storage.
     */
    public function store(Request $request, CreateContractAction $action)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'administration_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $contract = $action->execute($validated);

        return redirect()->route('commercial.contracts.index')
                         ->with('success', "Contrato {$contract->contract_number} generado exitosamente.");
    }

    /**
     * Download contract PDF.
     */
    public function downloadPdf(ContractService $contract)
    {
        // Asegurarnos de que el contrato cargue a su cliente
        $contract->load('client');

        $data = [
            'contract' => $contract,
            'client' => $contract->client,
            'date' => now()->format('d/m/Y')
        ];

        // Se usa view('pdfs.contract-template') a crear luego
        $pdf = Pdf::loadView('pdfs.contract-template', $data);

        return $pdf->download("Contrato_SISTEMA_CONTRATAMOS_{$contract->contract_number}.pdf");
    }
}
