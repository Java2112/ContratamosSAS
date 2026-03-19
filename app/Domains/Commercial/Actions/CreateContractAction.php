<?php

namespace App\Domains\Commercial\Actions;

use App\Domains\Commercial\Models\Client;
use App\Domains\Commercial\Models\ContractService;
use Illuminate\Support\Facades\DB;

class CreateContractAction
{
    /**
     * Crea un contrato de servicio comercial.
     * 
     * @param array $data
     * @return ContractService
     */
    public function execute(array $data): ContractService
    {
        return DB::transaction(function () use ($data) {
            
            $client = Client::findOrFail($data['client_id']);

            // Generar número de contrato correlativo (Ej: CNT-2026-0001)
            $year = date('Y');
            $latestContract = ContractService::where('contract_number', 'LIKE', "CNT-{$year}-%")->latest('id')->first();
            $nextSequence = $latestContract ? intval(substr($latestContract->contract_number, -4)) + 1 : 1;
            $contractNumber = "CNT-{$year}-" . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

            $contract = ContractService::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1, // Fix para multi-tenant si es por consola/test
                'client_id' => $client->id,
                'contract_number' => $contractNumber,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'administration_fee_percentage' => $data['administration_fee_percentage'] ?? 0,
                'status' => 'active',
            ]);

            // Registrar Log de Actividad
            activity()
                ->causedBy(auth()->user())
                ->performedOn($contract)
                ->event('contract_created')
                ->log("Se ha firmado un nuevo contrato {$contractNumber} con el cliente {$client->business_name}");

            return $contract;
        });
    }
}
