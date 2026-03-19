<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Domains\Commercial\Models\Client;
use App\Domains\Commercial\Models\ContractService;
use App\Enums\SystemArea;

class DashboardController extends Controller
{
    /**
     * Muestra el Dashboard principal dependiendo del Área del usuario logueado.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $area = $user->system_area;

        // Si es Gerencia (o no tiene área definida como el superadmin inicial) -> Dashboard Global Ejecutivo
        if ($area === SystemArea::GERENCIA || !$area) {
            $metrics = [
                'total_users' => User::count(),
                'active_clients' => Client::count(),
                'active_contracts' => ContractService::where('status', 'active')->count(),
            ];
            return Inertia::render('Dashboard/Executive', ['metrics' => $metrics]);
        }

        // Si es Comercial -> Dashboard Comercial
        if ($area === SystemArea::COMERCIAL) {
            $metrics = [
                'total_clients' => Client::count(),
                'recent_contracts' => ContractService::with('client')->latest()->take(5)->get(),
            ];
            return Inertia::render('Dashboard/Commercial', ['metrics' => $metrics]);
        }

        // Si es otra área genérica, retornamos un Dashboard genérico de área por el momento
        return Inertia::render('Dashboard/Area', [
            'area_name' => $area->label()
        ]);
    }
}
