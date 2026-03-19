<?php

namespace App\Domains\Commercial\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Commercial\Models\Client;
use App\Domains\Commercial\Actions\RegisterNewClientAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index()
    {
        // En un proyecto real, agregaríamos filtros y paginación
        $clients = Client::latest()->get();

        return Inertia::render('Commercial/Clients/Index', [
            'clients' => $clients
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return Inertia::render('Commercial/Clients/Create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request, RegisterNewClientAction $action)
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string|unique:clients,document_number',
            'business_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'industry_sector' => 'required|string|max:100',
        ]);

        $action->execute($validated);

        return redirect()->route('commercial.clients.index')->with('success', 'Cliente creado exitosamente. Las credenciales fueron enviadas.');
    }
}
